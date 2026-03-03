import { render, fireEvent } from '@testing-library/svelte';
import { describe, it, expect, vi } from 'vitest';
import Button from '../../components/ui/Button.svelte';
import { createRawSnippet } from 'svelte';

describe('Button', () => {
    it('renders with children', () => {
        const { getByText } = render(Button, {
            children: createRawSnippet(() => ({
                render: () => '<span>Click Me</span>'
            }))
        });
        expect(getByText('Click Me')).toBeTruthy();
    });

    it('handles click events', async () => {
        const handleClick = vi.fn();
        const { getByText } = render(Button, {
            children: createRawSnippet(() => ({
                render: () => '<span>Click Me</span>'
            })),
            onclick: handleClick
        });

        await fireEvent.click(getByText('Click Me'));
        expect(handleClick).toHaveBeenCalledTimes(1);
    });

    it('is disabled when the disabled prop is true', () => {
        const { getByRole } = render(Button, {
            children: createRawSnippet(() => ({
                render: () => '<span>Disabled</span>'
            })),
            disabled: true
        });
        const button = getByRole('button');
        expect(button.disabled).toBe(true);
        expect(button.classList.contains('disabled:opacity-50')).toBe(true);
    });
});
