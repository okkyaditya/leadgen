export default function Checkbox({ className = '', ...props }) {
    return (
        <input
            {...props}
            type="checkbox"
            className={
                'rounded border-gray-300 text-brand-accent shadow-sm focus:ring-brand-accent dark:border-gray-700 dark:bg-gray-900 dark:focus:ring-brand-accent dark:focus:ring-offset-gray-800 ' +
                className
            }
        />
    );
}
