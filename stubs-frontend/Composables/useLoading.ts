import { shallowRef, readonly } from 'vue';

/**
 * useLoading - Loading state management helper
 * 
 * @description Manages loading state with readonly protection
 * @param initialValue - Initial loading state (default: false)
 * @returns Loading state and control methods
 * 
 * @example
 * const { loading, start, stop, toggle } = useLoading();
 */
export function useLoading(initialValue = false) {
    const _loading = shallowRef(initialValue);

    function start() {
        _loading.value = true;
    }

    function stop() {
        _loading.value = false;
    }

    function toggle() {
        _loading.value = !_loading.value;
    }

    return {
        loading: readonly(_loading),
        start,
        stop,
        toggle
    };
}
