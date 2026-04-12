/**
 * Validation Utility Functions
 * 
 * BEST PRACTICE: These utilities are for CUSTOM validation logic.
 * For Inertia forms, use backend validation (Laravel FormRequest) + Inertia's form.errors
 * 
 * @see https://vuejs.org/guide/best-practices
 * @see https://inertiajs.com/validation
 */

export interface ValidationResult {
    valid: boolean;
    message?: string;
}

// ============================================================================
// BASIC VALIDATORS (Use with compose())
// ============================================================================

/**
 * Check if value is required
 * 
 * @example
 * if (!required(value).valid) { error = required(value).message; }
 */
export function required(value: any): ValidationResult {
    if (typeof value === 'string') {
        return {
            valid: value.trim().length > 0,
            message: 'This field is required'
        };
    }
    return {
        valid: value !== null && value !== undefined && value !== '',
        message: 'This field is required'
    };
}

/**
 * Validate email format
 */
export function email(value: string): ValidationResult {
    const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return {
        valid: pattern.test(value),
        message: 'Please enter a valid email address'
    };
}

/**
 * Validate phone number (Bangladesh format)
 */
export function phone(value: string): ValidationResult {
    const pattern = /^(\+880|880|0)?[1-9][0-9]{9}$/;
    return {
        valid: pattern.test(value.replace(/[\s-]/g, '')),
        message: 'Please enter a valid phone number'
    };
}

// ============================================================================
// LENGTH VALIDATORS
// ============================================================================

/**
 * Validate minimum length
 */
export function minLength(length: number) {
    return (value: string): ValidationResult => ({
        valid: value.length >= length,
        message: `Must be at least ${length} characters`
    });
}

/**
 * Validate maximum length
 */
export function maxLength(length: number) {
    return (value: string): ValidationResult => ({
        valid: value.length <= length,
        message: `Must not exceed ${length} characters`
    });
}

/**
 * Validate that a value matches another field's current value.
 *
 * Takes a getter so it always reads the live value at validation time,
 * not a stale snapshot captured when the rule is defined.
 *
 * @param getTarget - A function that returns the value to match against
 * @param message   - Optional custom error message
 *
 * @example
 * password_confirmation: [required, confirmed(() => form.password)],
 */
export function confirmed(getTarget: () => string, message = 'Passwords do not match') {
    return (value: string): ValidationResult => ({
        valid: value === getTarget(),
        message,
    });
}

// ============================================================================
// NUMBER VALIDATORS
// ============================================================================

/**
 * Validate minimum value
 */
export function minValue(min: number) {
    return (value: number | string): ValidationResult => {
        const num = typeof value === 'string' ? parseFloat(value) : value;
        return {
            valid: num >= min,
            message: `Must be at least ${min}`
        };
    };
}

/**
 * Validate maximum value
 */
export function maxValue(max: number) {
    return (value: number | string): ValidationResult => {
        const num = typeof value === 'string' ? parseFloat(value) : value;
        return {
            valid: num <= max,
            message: `Must not exceed ${max}`
        };
    };
}

/**
 * Validate positive number
 */
export function positive(value: number | string): ValidationResult {
    const num = typeof value === 'string' ? parseFloat(value) : value;
    return {
        valid: num > 0,
        message: 'Value must be positive'
    };
}

// ============================================================================
// DATE VALIDATORS
// ============================================================================

/**
 * Validate date is in the future
 */
export function futureDate(value: string | Date): ValidationResult {
    const date = typeof value === 'string' ? new Date(value) : value;
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    return {
        valid: date >= today,
        message: 'Date must be today or later'
    };
}

/**
 * Validate date range (start before end)
 * 
 * @example
 * const result = dateRange(form.checkIn, form.checkOut);
 * if (!result.valid) { error = result.message; }
 */
export function dateRange(start: string | Date, end: string | Date): ValidationResult {
    const startDate = typeof start === 'string' ? new Date(start) : start;
    const endDate = typeof end === 'string' ? new Date(end) : end;
    return {
        valid: startDate < endDate,
        message: 'End date must be after start date'
    };
}

/**
 * Validate check-in date (today or future)
 */
export function checkInDate(value: string): ValidationResult {
    if (!value) {
        return { valid: false, message: 'Check-in date is required' };
    }
    const checkIn = new Date(value);
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    return {
        valid: checkIn >= today,
        message: 'Check-in date must be today or later'
    };
}

/**
 * Validate check-out date (after check-in)
 */
export function checkOutDate(checkOut: string, checkIn: string): ValidationResult {
    if (!checkOut) {
        return { valid: false, message: 'Check-out date is required' };
    }
    if (!checkIn) {
        return { valid: true }; // Can't validate without check-in
    }
    const checkOutDate = new Date(checkOut);
    const checkInDate = new Date(checkIn);
    return {
        valid: checkOutDate > checkInDate,
        message: 'Check-out date must be after check-in date'
    };
}

// ============================================================================
// COMPOSE VALIDATORS (Combine multiple rules)
// ============================================================================

/**
 * Compose multiple validators
 * 
 * @example
 * const validateGuest = compose(
 *     required,
 *     (value) => ({ valid: value > 0, message: 'Invalid guest' })
 * );
 * 
 * const result = validateGuest(form.guest_id);
 * if (!result.valid) { error = result.message; }
 */
export function compose(...validators: Array<(value: any) => ValidationResult>) {
    return (value: any): ValidationResult => {
        for (const validator of validators) {
            const result = validator(value);
            if (!result.valid) {
                return result; // Return first error
            }
        }
        return { valid: true };
    };
}

// ============================================================================
// INERTIA FORM VALIDATION HELPER
// ============================================================================

/**
 * Set validation errors on Inertia form
 * 
 * BEST PRACTICE: Use this for client-side validation before submitting
 * Backend will validate again with Laravel FormRequest
 * 
 * @example
 * const form = useForm({ guest_id: '', room_id: '' });
 * 
 * function submit() {
 *     if (!validateInertiaForm(form, {
 *         guest_id: [required],
 *         room_id: [required],
 *     })) {
 *         return; // Don't submit
 *     }
 *     form.post('/reservations');
 * }
 */
export function validateInertiaForm<T extends Record<string, any>>(
    form: any,
    rules: Record<keyof T, Array<(value: any) => ValidationResult>>
): boolean {
    let isValid = true;
    form.clearErrors();

    for (const [field, validators] of Object.entries(rules)) {
        const value = form[field as keyof T];

        for (const validator of validators as Array<(value: any) => ValidationResult>) {
            const result = validator(value);
            if (!result.valid) {
                form.setError(field as keyof T, result.message!);
                isValid = false;
                break; // Stop at first error for this field
            }
        }
    }

    return isValid;
}