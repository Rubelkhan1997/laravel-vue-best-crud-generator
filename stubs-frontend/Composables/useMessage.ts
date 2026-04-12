import { shallowRef, readonly } from 'vue';

/**
 * useMessage - Message state management helper
 * 
 * @description Manages success/error messages with auto-clear
 * @param autoClearDelay - Auto-clear delay in milliseconds (default: 5000)
 * @returns Message state and control methods
 * 
 * @example
 * const { message, messageType, showMessage, clearMessage } = useMessage();
 * showMessage('Success!', 'success');
 */
export function useMessage(autoClearDelay = 5000) {
    const _message = shallowRef<string | null>(null);
    const _messageType = shallowRef<'success' | 'error' | null>(null);
    let timeoutId: ReturnType<typeof setTimeout> | null = null;

    function showMessage(msg: string, type: 'success' | 'error' = 'success') {
        // Clear existing timeout
        if (timeoutId) clearTimeout(timeoutId);

        _message.value = msg;
        _messageType.value = type;

        // Auto-clear
        timeoutId = setTimeout(() => {
            _message.value = null;
            _messageType.value = null;
        }, autoClearDelay);
    }

    function clearMessage() {
        if (timeoutId) clearTimeout(timeoutId);
        _message.value = null;
        _messageType.value = null;
    }

    return {
        message: readonly(_message),
        messageType: readonly(_messageType),
        showMessage,
        clearMessage
    };
}
