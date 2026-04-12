/**
 * Styles Barrel Export
 * Import this file to include all styles
 */

// Note: SCSS files are imported in main.scss
// This file is for TypeScript module declarations

declare module '*.scss' {
    const content: Record<string, string>;
    export default content;
}

declare module '*.css' {
    const content: Record<string, string>;
    export default content;
}

// Export CSS module classes (if using CSS modules)
// export { default as variables } from './variables.scss';
// export { default as mixins } from './mixins.scss';
