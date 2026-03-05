import { render, screen, fireEvent } from "@testing-library/svelte";
import { describe, it, expect, vi } from "vitest";
import OfferModal from "../../components/features/OfferModal.svelte";

describe("OfferModal", () => {
    const mockProperty = { id: 1, name: "Luxury Villa", price: 5000 };

    it("renders when open", () => {
        render(OfferModal, { isOpen: true, onClose: () => {}, property: mockProperty, onSubmit: () => {} });
        // Use getAllByRole if multiple exist, or just check one
        expect(screen.getAllByRole("heading", { name: /Make an Offer/i }).length).toBeGreaterThan(0);
        expect(screen.getByText(/Luxury Villa/i)).toBeTruthy();
        expect(screen.getByLabelText(/Monthly Rent/i)).toBeTruthy();
        expect(screen.getByLabelText(/Additional Terms/i)).toBeTruthy();
    });

    it("calls onSubmit with form data", async () => {
        const onSubmit = vi.fn().mockResolvedValue(true);
        render(OfferModal, { 
            isOpen: true, 
            onClose: () => {}, 
            property: mockProperty, 
            onSubmit 
        });

        await fireEvent.input(screen.getByLabelText(/Monthly Rent/i), { target: { value: "4500" } });
        await fireEvent.input(screen.getByLabelText(/Additional Terms/i), { target: { value: "Included utilities" } });
        
        await fireEvent.submit(screen.getByRole("button", { name: /Send Offer/i }));

        expect(onSubmit).toHaveBeenCalledWith(4500, "Included utilities");
    });

    it("shows submitting state", () => {
        render(OfferModal, { 
            isOpen: true, 
            onClose: () => {}, 
            property: mockProperty, 
            onSubmit: () => {},
            isSubmitting: true 
        });
        expect(screen.getByText(/Signing in.../i)).toBeTruthy(); // Based on actual component code
    });
});
