<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Core\Response;
use App\Models\User;
use App\Security\Sanitizer;
use App\Security\SecurityAuditLog;
use App\Security\SecurityHeaders;
use App\Testing\TestCase;

class SecurityAuditingAndHardeningTest extends TestCase {
    public function testSecurityAuditLogCreationAndHashing(): void {
        $log1 = SecurityAuditLog::log('auth.login.success', 'info', 10, 'alice@shilpa.lk', ['ip' => '127.0.0.1']);
        assert(!empty($log1->integrity_hash), "Integrity hash must be generated");
        assert(strlen((string)$log1->integrity_hash) === 64, "Integrity hash must be 64 characters (SHA256 HMAC)");
        assert($log1->previous_hash === 'GENESIS_BLOCK_SHILPA_2026', "Initial log must link to genesis block");

        $log2 = SecurityAuditLog::log('course.create', 'info', 10, 'alice@shilpa.lk', ['course_id' => 42]);
        assert($log2->previous_hash === $log1->integrity_hash, "Second log must chain to first log hash");
    }

    public function testChainIntegrityVerificationSucceedsWhenUntampered(): void {
        $result = SecurityAuditLog::verifyChainIntegrity();
        assert($result['intact'] === true, "Audit chain must verify as intact when untampered");
        assert($result['compromised_at'] === null, "No records should be compromised");
    }

    public function testChainIntegrityDetectsTamperedRecords(): void {
        $tamperedLog = SecurityAuditLog::log('system.sensitive_action', 'critical', 99, 'badactor@shilpa.lk', ['target' => 'admin_role']);
        
        // Directly tamper with the database record payload
        SecurityAuditLog::query()
            ->where('id', $tamperedLog->id)
            ->update(['payload' => '{"tampered":true,"target":"altered"}']);

        $result = SecurityAuditLog::verifyChainIntegrity();
        assert($result['intact'] === false, "Audit chain must detect tampered payload");
        assert($result['compromised_at'] === (int)$tamperedLog->id, "Compromised record ID must match tampered log");
    }

    public function testHardenedSecurityHeadersAppliedToResponse(): void {
        $response = new Response('<html><body>Secured Content</body></html>');
        SecurityHeaders::apply($response);

        $headers = $response->getHeaders();
        assert(isset($headers['Content-Security-Policy']), "CSP header must be present");
        assert(str_contains($headers['Content-Security-Policy'], "default-src 'self'"), "CSP must set default-src 'self'");
        assert(isset($headers['X-Frame-Options']) && $headers['X-Frame-Options'] === 'DENY', "X-Frame-Options must be DENY");
        assert(isset($headers['X-Content-Type-Options']) && $headers['X-Content-Type-Options'] === 'nosniff', "X-Content-Type-Options must be nosniff");
        assert(isset($headers['Referrer-Policy']), "Referrer-Policy must be set");
        assert(isset($headers['Permissions-Policy']), "Permissions-Policy must be set");
    }

    public function testDispatchedHttpRequestsReceiveSecurityHeaders(): void {
        $response = $this->get('/health');
        $headers = $response->getHeaders();
        assert(isset($headers['X-Frame-Options']), "Dispatched route must contain X-Frame-Options");
        assert(isset($headers['Content-Security-Policy']), "Dispatched route must contain CSP");
    }

    public function testSanitizerStripsDangerousTagsAndTraversals(): void {
        $malicious = "<script>alert('XSS')</script>Hello <b>World</b>";
        $cleaned = Sanitizer::cleanString($malicious);
        assert(!str_contains($cleaned, '<script>'), "Sanitizer must strip script tags");
        assert(!str_contains($cleaned, '</script>'), "Sanitizer must strip closing script tags");

        $dangerousFilename = "../../etc/passwd.php";
        $cleanFilename = Sanitizer::sanitizeFilename($dangerousFilename);
        assert(!str_contains($cleanFilename, '..'), "Directory traversal must be stripped from filename");
        assert($cleanFilename === 'passwd.php', "Cleaned filename must match safe basename");
    }

    public function testSecurityAuditEndpointRequiresAdmin(): void {
        session()->clear();
        $unauthenticated = $this->get('/admin/security-audit');
        assert($unauthenticated->getStatusCode() === 302, "Unauthenticated access must redirect");

        // Authenticated as student
        session()->set('user', 'Student User');
        session()->set('user_role', 'student');
        $studentAccess = $this->get('/admin/security-audit');
        assert($studentAccess->getStatusCode() === 302, "Student must be redirected away from security audit");

        // Authenticated as admin
        session()->set('user', 'SecOps Admin');
        session()->set('user_role', 'admin');
        $adminAccess = $this->get('/admin/security-audit');
        assert($adminAccess->getStatusCode() === 200, "Admin must be able to view security audit console");
        $adminAccess->assertSee('Security Operations');
        $adminAccess->assertSee('Audit Trail');
    }
}
