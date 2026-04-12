/**
 * usePolling - Polling helper for periodic function execution
 * 
 * @description Polls a function at regular intervals
 * @param callback - Async function to poll
 * @param intervalMs - Interval in milliseconds
 * @param enabled - Function to check if polling is enabled
 * @returns Start and stop methods
 * 
 * @example
 * const { start, stop } = usePolling(
 *     () => fetchData(),
 *     30000,
 *     () => true
 * );
 */
export function usePolling(
    callback: () => Promise<void>,
    intervalMs: number,
    enabled: () => boolean
) {
    let intervalId: ReturnType<typeof setInterval> | null = null;

    function start() {
        if (!enabled()) return;

        // Immediate first call
        callback();

        // Then poll at interval
        intervalId = setInterval(() => {
            if (enabled()) {
                callback();
            }
        }, intervalMs);
    }

    function stop() {
        if (intervalId) {
            clearInterval(intervalId);
            intervalId = null;
        }
    }

    return { start, stop };
}
