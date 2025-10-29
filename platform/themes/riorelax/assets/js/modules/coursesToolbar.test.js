import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest'
import { initCoursesToolbar, mapSortValueToFields, resolveSortValue, SORT_MAPPINGS } from './coursesToolbar'

describe('coursesToolbar sort utils', () => {
    it('maps sort tokens', () => {
        expect(mapSortValueToFields('price_desc')).toEqual({ sort_by: 'price', sort_direction: 'desc' })
        expect(mapSortValueToFields('unknown')).toEqual(SORT_MAPPINGS.upcoming_asc)
    })

    it('derives token from tuple', () => {
        expect(resolveSortValue('created_at', 'desc')).toBe('created_desc')
        expect(resolveSortValue(null, 'asc')).toBe('upcoming_asc')
    })
})

describe('initCoursesToolbar', () => {
    const originalScrollY = Object.getOwnPropertyDescriptor(window, 'scrollY')
    const originalInnerWidth = Object.getOwnPropertyDescriptor(window, 'innerWidth')

    beforeEach(() => {
        document.body.innerHTML = `
            <div data-course-toolbar data-state="closed" class="courses-toolbar" style="top: 48px;">
                <div class="courses-toolbar__bar">
                    <button data-course-toolbar-toggle aria-expanded="false">Toggle</button>
                </div>
                <div class="courses-toolbar__overlay" data-course-toolbar-backdrop hidden></div>
                <form data-course-toolbar-panel>
                    <select name="category_id"><option value="">Alle</option><option value="1">Yoga</option></select>
                    <select name="instructor_id"><option value="">Alle</option><option value="1">Anna</option></select>
                    <input type="date" name="start_date" value="2024-06-01" />
                    <select data-course-sort-select data-initial-sort="price_desc">
                        <option value="upcoming_asc">Up</option>
                        <option value="price_desc">Preis Desc</option>
                    </select>
                    <a href="#" data-course-toolbar-reset>Zurücksetzen</a>
                    <input type="hidden" name="sort_by" value="" data-course-sort-by />
                    <input type="hidden" name="sort_direction" value="" data-course-sort-direction />
                </form>
            </div>
        `

        vi.spyOn(window, 'getComputedStyle').mockImplementation(element =>
            element.hasAttribute?.('data-course-toolbar') ? { top: '48px' } : window.getComputedStyle(element)
        )

        Object.defineProperty(document.querySelector('[data-course-toolbar]'), 'offsetTop', { configurable: true, value: 180 })
        if (originalInnerWidth) Object.defineProperty(window, 'innerWidth', { configurable: true, writable: true, value: 500 })
        if (originalScrollY) Object.defineProperty(window, 'scrollY', { configurable: true, writable: true, value: 0 })
    })

    afterEach(() => {
        vi.restoreAllMocks()
        document.body.innerHTML = ''
        document.documentElement.classList.remove('courses-toolbar-scroll-lock')
        if (originalInnerWidth) Object.defineProperty(window, 'innerWidth', originalInnerWidth)
        if (originalScrollY) Object.defineProperty(window, 'scrollY', originalScrollY)
    })

    it('syncs sort inputs and auto submits', () => {
        const form = document.querySelector('form')
        form.requestSubmit = vi.fn()
        initCoursesToolbar(document, window)

        const sortByInput = form.querySelector('[data-course-sort-by]')
        const sortDirectionInput = form.querySelector('[data-course-sort-direction]')
        const sortSelect = form.querySelector('[data-course-sort-select]')

        expect(sortSelect.value).toBe('price_desc')
        expect(sortByInput.value).toBe('price')
        expect(sortDirectionInput.value).toBe('desc')

        sortSelect.value = 'upcoming_asc'
        sortSelect.dispatchEvent(new Event('change', { bubbles: true }))

        expect(sortByInput.value).toBe('upcoming_session')
        expect(sortDirectionInput.value).toBe('asc')
        expect(form.requestSubmit).toHaveBeenCalled()
    })

    it('resets filters and clears scroll lock', () => {
        const form = document.querySelector('form')
        form.requestSubmit = vi.fn()
        initCoursesToolbar(document, window)

        form.querySelector('[name="category_id"]').value = '1'
        document.documentElement.classList.add('courses-toolbar-scroll-lock')
        form.querySelector('[data-course-toolbar-reset]').dispatchEvent(new Event('click', { bubbles: true, cancelable: true }))

        expect(form.querySelector('[name="category_id"]').value).toBe('')
        expect(document.documentElement.classList.contains('courses-toolbar-scroll-lock')).toBe(false)
        expect(form.requestSubmit).toHaveBeenCalledTimes(1)
    })

    it('toggles panel and applies floating class', () => {
        initCoursesToolbar(document, window)
        const toolbar = document.querySelector('[data-course-toolbar]')
        const toggle = toolbar.querySelector('[data-course-toolbar-toggle]')
        const overlay = toolbar.querySelector('[data-course-toolbar-backdrop]')

        toggle.dispatchEvent(new Event('click', { bubbles: true }))
        expect(toolbar.getAttribute('data-state')).toBe('open')
        expect(overlay.hidden).toBe(false)
        expect(document.documentElement.classList.contains('courses-toolbar-scroll-lock')).toBe(true)

        overlay.dispatchEvent(new Event('click', { bubbles: true }))
        expect(toolbar.getAttribute('data-state')).toBe('closed')
        expect(overlay.hidden).toBe(true)

        window.scrollY = 320
        window.dispatchEvent(new Event('scroll'))
        expect(toolbar.classList.contains('courses-toolbar--floating')).toBe(true)
    })
})
