/**
 * SHILPA LMS — Global Command Palette & Quick Search (Cmd+K)
 */
(function () {
    let modal = null;
    let input = null;
    let resultsContainer = null;
    let debounceTimer = null;
    let activeIndex = -1;
    let items = [];

    function init() {
        modal = document.getElementById('command-palette-modal');
        if (!modal) return;

        input = document.getElementById('command-palette-input');
        resultsContainer = document.getElementById('command-palette-results');

        // Global hotkey: Cmd+K or Ctrl+K
        window.addEventListener('keydown', function (e) {
            if ((e.metaKey || e.ctrlKey) && (e.key === 'k' || e.key === 'K')) {
                e.preventDefault();
                toggleCommandPalette();
            } else if (e.key === 'Escape' && isPaletteOpen()) {
                e.preventDefault();
                closeCommandPalette();
            }
        });

        // Trigger button in header
        const trigger = document.getElementById('global-search-trigger');
        if (trigger) {
            trigger.addEventListener('click', function (e) {
                e.preventDefault();
                openCommandPalette();
            });
        }

        // Close backdrop click
        modal.addEventListener('click', function (e) {
            if (e.target === modal) {
                closeCommandPalette();
            }
        });

        // Close button
        const closeBtn = document.getElementById('command-palette-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', closeCommandPalette);
        }

        // Input search listener
        if (input) {
            input.addEventListener('input', function () {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    performSearch(input.value.trim());
                }, 180);
            });

            input.addEventListener('keydown', handleKeyNavigation);
        }
    }

    function isPaletteOpen() {
        return modal && modal.getAttribute('aria-hidden') === 'false';
    }

    function openCommandPalette() {
        if (!modal) return;
        modal.setAttribute('aria-hidden', 'false');
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        if (input) {
            input.value = '';
            input.focus();
        }
        performSearch('');
    }

    function closeCommandPalette() {
        if (!modal) return;
        modal.setAttribute('aria-hidden', 'true');
        modal.style.display = 'none';
        document.body.style.overflow = '';
        activeIndex = -1;
        items = [];
    }

    function toggleCommandPalette() {
        if (isPaletteOpen()) {
            closeCommandPalette();
        } else {
            openCommandPalette();
        }
    }

    function handleKeyNavigation(e) {
        if (items.length === 0) return;

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            activeIndex = (activeIndex + 1) % items.length;
            updateActiveItem();
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            activeIndex = (activeIndex - 1 + items.length) % items.length;
            updateActiveItem();
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (activeIndex >= 0 && activeIndex < items.length) {
                const targetUrl = items[activeIndex].getAttribute('data-url');
                if (targetUrl) {
                    window.location.href = targetUrl;
                }
            } else if (input && input.value.trim() !== '') {
                window.location.href = '/search?q=' + encodeURIComponent(input.value.trim());
            }
        }
    }

    function updateActiveItem() {
        items.forEach((item, index) => {
            if (index === activeIndex) {
                item.classList.add('command-item-active');
                item.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
            } else {
                item.classList.remove('command-item-active');
            }
        });
    }

    function performSearch(query) {
        if (!resultsContainer) return;
        resultsContainer.innerHTML = '<div style="padding: 2rem; text-align: center; color: var(--text-muted); font-size: 0.875rem;">Loading results...</div>';

        fetch('/api/search?q=' + encodeURIComponent(query))
            .then(res => res.json())
            .then(data => {
                renderResults(data.results || [], query);
            })
            .catch(() => {
                resultsContainer.innerHTML = '<div style="padding: 2rem; text-align: center; color: var(--text-muted); font-size: 0.875rem;">Unable to load search results.</div>';
            });
    }

    function renderResults(results, query) {
        if (!resultsContainer) return;

        if (results.length === 0) {
            resultsContainer.innerHTML = `
                <div style="padding: 2.5rem 1rem; text-align: center; color: var(--text-muted);">
                    <div style="font-size: 0.9375rem; font-weight: 600; color: var(--text-color); margin-bottom: 0.25rem;">No results found</div>
                    <div style="font-size: 0.8125rem;">Press Enter to search globally for "${escapeHtml(query)}"</div>
                </div>
            `;
            items = [];
            activeIndex = -1;
            return;
        }

        // Group by category
        const groups = {};
        results.forEach(res => {
            const cat = res.category || 'Results';
            if (!groups[cat]) groups[cat] = [];
            groups[cat].push(res);
        });

        let html = '';
        Object.keys(groups).forEach(categoryName => {
            html += `<div style="font-size: 0.6875rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); padding: 0.625rem 1rem 0.25rem 1rem;">${escapeHtml(categoryName)}</div>`;
            groups[categoryName].forEach(item => {
                html += `
                    <a href="${escapeHtml(item.url)}" class="command-item" data-url="${escapeHtml(item.url)}" style="display: flex; align-items: center; gap: 0.875rem; padding: 0.625rem 1rem; text-decoration: none; border-radius: var(--radius-md); margin-inline: 0.5rem; transition: background 0.15s; color: var(--text-color);">
                        <div style="width: 28px; height: 28px; border-radius: 6px; background: var(--surface-secondary); display: flex; align-items: center; justify-content: center; color: var(--primary); flex-shrink: 0; font-size: 0.8125rem;">
                            ✦
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <div style="font-size: 0.875rem; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                ${escapeHtml(item.title)}
                            </div>
                            <div style="font-size: 0.75rem; color: var(--text-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                ${escapeHtml(item.subtitle || '')}
                            </div>
                        </div>
                        <span style="font-size: 0.6875rem; color: var(--text-muted); background: var(--surface-secondary); padding: 0.125rem 0.375rem; border-radius: 4px; border: 1px solid var(--border-color);">↵ Jump</span>
                    </a>
                `;
            });
        });

        resultsContainer.innerHTML = html;
        items = Array.from(resultsContainer.querySelectorAll('.command-item'));
        activeIndex = items.length > 0 ? 0 : -1;
        updateActiveItem();
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
