const STORAGE_KEY = 'lotixam-me-theme';

function getStored() {
    const v = localStorage.getItem(STORAGE_KEY);
    if (v === 'light' || v === 'dark' || v === 'system') {
        return v;
    }
    return 'system';
}

function applyToRoot(mode) {
    const root = document.documentElement;
    root.classList.remove('me-dark');
    if (mode === 'dark') {
        root.classList.add('me-dark');
    } else if (mode === 'light') {
        /* clair forcé */
    } else if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
        root.classList.add('me-dark');
    }
}

let mql;

function bindSystemListener() {
    if (mql) {
        return;
    }
    mql = window.matchMedia('(prefers-color-scheme: dark)');
    mql.addEventListener('change', () => {
        if (getStored() === 'system') {
            applyToRoot('system');
        }
    });
}

export function setMeTheme(mode) {
    if (mode !== 'light' && mode !== 'dark' && mode !== 'system') {
        return;
    }
    localStorage.setItem(STORAGE_KEY, mode);
    applyToRoot(mode);
    if (mode === 'system') {
        bindSystemListener();
    }
}

function updateToggleUi() {
    const current = getStored();
    document.querySelectorAll('[data-me-theme]').forEach((el) => {
        const m = el.getAttribute('data-me-theme');
        const active = m === current;
        el.setAttribute('aria-pressed', active ? 'true' : 'false');
        el.classList.toggle('ring-2', active);
        el.classList.toggle('ring-[#b1e90e]', active);
        el.classList.toggle('ring-offset-2', active);
    });
}

function init() {
    if (!document.body?.hasAttribute('data-me-theme-page')) {
        document.documentElement.classList.remove('me-dark');
        return;
    }
    applyToRoot(getStored());
    if (getStored() === 'system') {
        bindSystemListener();
    }
    document.querySelectorAll('[data-me-theme]').forEach((el) => {
        el.addEventListener('click', (e) => {
            e.preventDefault();
            setMeTheme(el.getAttribute('data-me-theme'));
            updateToggleUi();
        });
    });
    updateToggleUi();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
} else {
    init();
}
