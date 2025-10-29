export const SORT_MAPPINGS = {
    upcoming_asc: { sort_by: 'upcoming_session', sort_direction: 'asc' },
    upcoming_desc: { sort_by: 'upcoming_session', sort_direction: 'desc' },
    created_desc: { sort_by: 'created_at', sort_direction: 'desc' },
    created_asc: { sort_by: 'created_at', sort_direction: 'asc' },
    price_asc: { sort_by: 'price', sort_direction: 'asc' },
    price_desc: { sort_by: 'price', sort_direction: 'desc' },
}

export const resolveSortValue = (sortBy, sortDirection) => {
    if (! sortBy) return 'upcoming_asc'
    const entry = Object.entries(SORT_MAPPINGS).find(([, value]) => value.sort_by === sortBy && value.sort_direction === (sortDirection || 'asc'))
    return entry ? entry[0] : 'upcoming_asc'
}

export const mapSortValueToFields = value => SORT_MAPPINGS[value] || SORT_MAPPINGS.upcoming_asc

const requestSubmit = form => {
    if (! form) return
    typeof form.requestSubmit === 'function' ? form.requestSubmit() : form.submit()
}

export const initCoursesToolbar = (root = document, win = window) => {
    const toolbar = root.querySelector('[data-course-toolbar]')
    if (! toolbar) return

    const panel = toolbar.querySelector('[data-course-toolbar-panel]')
    const toggle = toolbar.querySelector('[data-course-toolbar-toggle]')
    const overlay = toolbar.querySelector('[data-course-toolbar-backdrop]')
    const sortSelect = toolbar.querySelector('[data-course-sort-select]')
    const sortByInput = toolbar.querySelector('[data-course-sort-by]')
    const sortDirectionInput = toolbar.querySelector('[data-course-sort-direction]')
    const resetLink = toolbar.querySelector('[data-course-toolbar-reset]')
    const form = panel && panel.tagName === 'FORM' ? panel : toolbar.querySelector('form')

    if (! toolbar.getAttribute('data-state')) toolbar.setAttribute('data-state', 'closed')

    const lockScrollClass = 'courses-toolbar-scroll-lock'
    const lockScroll = () => { if (win.innerWidth <= 768) document.documentElement.classList.add(lockScrollClass) }
    const unlockScroll = () => document.documentElement.classList.remove(lockScrollClass)

    const openPanel = () => {
        toolbar.setAttribute('data-state', 'open')
        toggle?.setAttribute('aria-expanded', 'true')
        if (overlay) overlay.hidden = false
        lockScroll()
    }

    const closePanel = (focusToggle = false) => {
        toolbar.setAttribute('data-state', 'closed')
        toggle?.setAttribute('aria-expanded', 'false')
        if (overlay) overlay.hidden = true
        unlockScroll()
        if (focusToggle && toggle) toggle.focus()
    }

    const handleResize = () => { if (win.innerWidth > 768) closePanel() }

    const updateFloatingState = (() => {
        let threshold = 0
        const recalc = () => {
            const topValue = parseFloat(win.getComputedStyle(toolbar).top) || 0
            threshold = Math.max(toolbar.offsetTop - topValue, 0)
        }
        const sync = () => {
            const isFloating = win.scrollY >= threshold && win.scrollY > 0
            toolbar.classList.toggle('courses-toolbar--floating', isFloating)
        }
        recalc(); sync()
        win.addEventListener('resize', () => { recalc(); sync() })
        return () => sync()
    })()

    win.addEventListener('scroll', () => updateFloatingState(), { passive: true })
    win.addEventListener('keydown', event => { if (event.key === 'Escape' && toolbar.getAttribute('data-state') === 'open') closePanel(true) })
    win.addEventListener('resize', handleResize)

    if (toggle) {
        toggle.addEventListener('click', () => toolbar.getAttribute('data-state') === 'open' ? closePanel(true) : openPanel())
    }

    overlay?.addEventListener('click', () => closePanel(true))

    if (sortSelect && sortByInput && sortDirectionInput) {
        const initial = sortSelect.getAttribute('data-initial-sort')
        sortSelect.value = initial && SORT_MAPPINGS[initial] ? initial : resolveSortValue(sortByInput.value, sortDirectionInput.value)
        const current = mapSortValueToFields(sortSelect.value)
        sortByInput.value = current.sort_by
        sortDirectionInput.value = current.sort_direction
        sortSelect.addEventListener('change', () => {
            const mapping = mapSortValueToFields(sortSelect.value)
            sortByInput.value = mapping.sort_by
            sortDirectionInput.value = mapping.sort_direction
            requestSubmit(form)
        })
    }

    resetLink?.addEventListener('click', event => {
        event.preventDefault()
        if (! form) return
        form.querySelectorAll('[name="category_id"], [name="instructor_id"], [name="start_date"]').forEach(control => {
            if ('value' in control) control.value = ''
        })
        const mapping = mapSortValueToFields('upcoming_asc')
        if (sortSelect) sortSelect.value = 'upcoming_asc'
        if (sortByInput && sortDirectionInput) {
            sortByInput.value = mapping.sort_by
            sortDirectionInput.value = mapping.sort_direction
        }
        requestSubmit(form)
    })

    handleResize()
}
