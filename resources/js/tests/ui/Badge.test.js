import { render } from '@testing-library/svelte';
import { describe, it, expect } from 'vitest';
import Badge from '../../components/ui/Badge.svelte';
import { createRawSnippet } from 'svelte';

describe('Badge', () => {
    it('renders with children', () => {
        const { getByText } = render(Badge, {
            children: createRawSnippet(() => ({
                render: () => '<span>Active</span>'
            }))
        });
        expect(getByText('Active')).toBeTruthy();
    });

    it('applies type classes correctly', () => {
        const { getByText } = render(Badge, {
            children: createRawSnippet(() => ({
                render: () => '<span>Success</span>'
            })),
            type: 'success'
        });
        const badge = getByText('Success').parentElement;
        expect(badge.classList.contains('bg-green-100')).toBe(true);
    });
});
