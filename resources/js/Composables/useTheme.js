import { ref } from 'vue'

const getInitialTheme = () => {
  if (typeof window !== 'undefined') {
    const stored = localStorage.getItem('orbit-theme')
    if (stored === 'light' || stored === 'dark') {
      return stored
    }
    return document.documentElement.classList.contains('dark') ? 'dark' : 'light'
  }
  return 'dark'
}

const theme = ref(getInitialTheme())

export function useTheme() {
  const applyTheme = (newTheme) => {
    theme.value = newTheme
    if (typeof document !== 'undefined') {
      const root = document.documentElement
      if (newTheme === 'dark') {
        root.classList.add('dark')
      } else {
        root.classList.remove('dark')
      }
      try {
        localStorage.setItem('orbit-theme', newTheme)
      } catch (e) {
        console.error(e)
      }
    }
  }

  const toggleTheme = () => {
    applyTheme(theme.value === 'dark' ? 'light' : 'dark')
  }

  return {
    theme,
    toggleTheme,
    isDark: () => theme.value === 'dark',
  }
}
