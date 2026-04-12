# Form Components (Bangla Guide)

এই ফোল্ডারে reusable form component রাখা হয়েছে যাতে FrontDesk/অন্যান্য page-এ একই UI pattern বারবার লেখা না লাগে।

## 1) `FormInput.vue`
- কাজ: text/email/number input render করে।

- গুরুত্বপূর্ণ `props`:
  - `modelValue`: parent থেকে আসা current value
  - `label`: input label text
  - `required`: `*` দেখানোর জন্য
  - `error`: validation message দেখানোর জন্য
  - `min/max/step`: number/date-like constraint

- গুরুত্বপূর্ণ function:
  - `onInput(event)`: input change হলে নতুন value parent-এ পাঠায়
  - `emit`: 
    - `update:modelValue`: `v-model` update করে
    - `blur`: parent চাইলে blur ধরতে পারে

## 2) `FormTextarea.vue`
- কাজ: multi-line text input (যেমন notes, address)।
- `FormInput`-এর মতোই `label`, `required`, `error`, `v-model` pattern follow করে।
- function:
  - `onInput(event)`: textarea value parent-এ পাঠায়।

## 3) `FormSelect.vue`
- কাজ: dropdown/select field বানায়।
- গুরুত্বপূর্ণ `props`:
  - `options`: option list
  - `optionLabel` / `optionValue`: object থেকে label/value key কোনটা হবে
  - `placeholder`: প্রথম empty option text
- function:
  - `onChange(event)`: selected value parent-এ পাঠায়
- `emit`: 
  - `update:modelValue`, `blur`

## 4) `FormRadio.vue`
- কাজ: single radio option component (group করে use করা হয়)।
- `props`:
  - `value`: এই radio-এর value
  - `modelValue`: selected value (v-model)
  - `current`: fallback preselected value (legacy support)
- computed:
  - `isChecked`: কোন radio checked হবে তা নির্ধারণ করে
- function:
  - `onChange()`: radio select হলে value emit করে

## 5) `DatePicker.vue`
- কাজ: `type="date"` input wrapper।
- `props`:
  - `min`, `max`: date range limit
  - `helperText`, `error`: নিচে message দেখানো
- function:
  - `onInput(event)`: date change হলে parent form update

## 6) `TimePicker.vue`
- কাজ: `type="time"` input wrapper।
- `props`:
  - `step`, `min`, `max`
  - `error`, `helperText`
- function:
  - `onInput(event)`: selected time emit করে

## 7) `FileUpload.vue`
- কাজ: file input handle করে (single/multiple)।
- `props`:
  - `multiple`: একাধিক file upload
  - `accept`: allowed file type filter
- computed:
  - `selectedText`: selected file name(s) text তৈরি করে
- function:
  - `onFileChange(event)`: `File | File[] | null` normalize করে emit করে

## 8) `FormButton.vue`
- কাজ: button style centralize করে।
- `props`:
  - `type`: `button|submit|reset`
  - `color`: semantic color (`primary`, `danger` ইত্যাদি)
  - `disabled`, `name`
- computed:
  - `colorClass`: color নাম থেকে Tailwind class map করে

## 9) `index.ts`
- কাজ: এই ফোল্ডারের সব component এক জায়গা থেকে export করা।
- সুবিধা: import ছোট হয়, যেমন:
  - `import { FormInput, FormSelect, FormButton } from '@/Components/Form';`

---

## `props`, `emit`, `onInput` সহজ ভাষায়
- `props` = Parent থেকে Child-এ data/config আসে
- `emit` = Vue.js এ emit মানে হলো — child component থেকে parent component কে event/data পাঠানো।
- `onInput` / `onChange` = user field change করলে নতুন value emit করার function

এই pattern এর কারণে `v-model` কাজ করে:
1. Parent value পাঠায় (`modelValue`)
2. Child field change detect করে
3. Child `emit('update:modelValue', newValue)` পাঠায়
4. Parent state update হয়


Visual flow
------------------
Parent (name)
   ↓
props.modelValue
   ↓
Input field (user types)
   ↓
onInput()
   ↓
emit('update:modelValue', value)
   ↓
Parent (name updated)