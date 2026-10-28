import { InputHTMLAttributes } from 'react';

export default function Checkbox({
    className = '',
    ...props
}: InputHTMLAttributes<HTMLInputElement>) {
    return (
        <input
            {...props}
            type="checkbox"
            className={
                'rounded border-stone-300 text-mustard-500 shadow-sm focus:ring-mustard-500 ' +
                className
            }
        />
    );
}
