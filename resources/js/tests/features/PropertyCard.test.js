import { render, fireEvent } from '@testing-library/svelte';
import { describe, it, expect, vi } from 'vitest';
import PropertyCard from '../../components/features/PropertyCard.svelte';

describe('PropertyCard', () => {
    const mockProperty = {
        id: 1,
        name: 'Test House',
        address: '123 Test St',
        price: 1500,
        status: 'available',
        type: 'House'
    };

    it('renders property details', () => {
        const { getByText } = render(PropertyCard, {
            property: mockProperty,
            onViewDetails: () => {}
        });

        expect(getByText('Test House')).toBeTruthy();
        expect(getByText('123 Test St')).toBeTruthy();
        expect(getByText('$1,500')).toBeTruthy();
    });

    it('calls onViewDetails when button is clicked', async () => {
        const handleViewDetails = vi.fn();
        const { getByText } = render(PropertyCard, {
            property: mockProperty,
            onViewDetails: handleViewDetails
        });

        await fireEvent.click(getByText('View Details'));
        expect(handleViewDetails).toHaveBeenCalledWith(mockProperty);
    });
});
