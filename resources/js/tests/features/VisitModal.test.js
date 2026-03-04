import { render, screen, fireEvent } from "@testing-library/svelte";
import { describe, it, expect, vi } from "vitest";
import VisitModal from "../../components/features/VisitModal.svelte";

describe("VisitModal", () => {
    const mockProperty = { id: 1, name: "Test Property", price: 2000 };

    it("shows scheduling form initially", () => {
        render(VisitModal, {
            isOpen: true,
            onClose: () => {},
            property: mockProperty,
            onSubmit: () => {}
        });

        expect(screen.getByText("When would you like to visit?")).toBeTruthy();
        expect(screen.getByLabelText("Date")).toBeTruthy();
        expect(screen.getByLabelText("Time")).toBeTruthy();
    });

    it("transitions to identity verification step", async () => {
        render(VisitModal, {
            isOpen: true,
            onClose: () => {},
            property: mockProperty,
            onSubmit: () => {}
        });

        await fireEvent.input(screen.getByLabelText("Date"), { target: { value: "2026-03-10" } });
        await fireEvent.input(screen.getByLabelText("Time"), { target: { value: "14:00" } });
        
        const nextButton = screen.getByText("Next: Verify Identity");
        await fireEvent.click(nextButton);

        expect(screen.getByText("Identity Verification")).toBeTruthy();
    });
});
