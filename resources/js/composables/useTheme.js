import { ref, watch, onMounted } from 'vue';

const THEME_KEY = 'supply4me-theme';

const theme = ref('light');

function applyTheme(newTheme) {
    const root = document.documentElement;
    if (newTheme === 'dark') {
        root.classList.add('dark');
    } else {
        root.classList.remove('dark');
    }
    theme.value = newTheme;
    localStorage.setItem(THEME_KEY, newTheme);
}

function toggleTheme() {
    applyTheme(theme.value === 'dark' ? 'light' : 'dark');
}

export function useTheme() {
    onMounted(() => {
        const saved = localStorage.getItem(THEME_KEY);
        if (saved) {
            applyTheme(saved);
        } else if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
            applyTheme('dark');
        } else {
            applyTheme('light');
        }
    });

    return { theme, toggleTheme };
}
