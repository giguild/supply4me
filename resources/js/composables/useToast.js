import { reactive } from 'vue';

const state = reactive({
    show: false,
    message: '',
    type: 'success',
});

export function useToast() {
    const show = (message, type = 'success') => {
        state.message = message;
        state.type = type;
        state.show = true;
        setTimeout(() => { state.show = false; }, 3000);
    };

    const success = (message) => show(message, 'success');
    const error = (message) => show(message, 'error');
    const warning = (message) => show(message, 'warning');
    const info = (message) => show(message, 'info');

    return { state, show, success, error, warning, info };
}
