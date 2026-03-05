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

    it("completes the full flow with document upload", async () => {
        const onSubmit = vi.fn().mockResolvedValue(true);
        render(VisitModal, {
            isOpen: true,
            onClose: () => {},
            property: mockProperty,
            onSubmit
        });

        // Step 1
        await fireEvent.input(screen.getByLabelText("Date"), { target: { value: "2026-03-10" } });
        await fireEvent.input(screen.getByLabelText("Time"), { target: { value: "14:00" } });
        await fireEvent.click(screen.getByText("Next: Verify Identity"));

        // Step 2
        const fileInput = document.getElementById('visit-identity-file');
        const file = new File(["test"], "id.png", { type: "image/png" });
        await fireEvent.change(fileInput, { target: { files: [file] } });
        
        await fireEvent.click(screen.getByText("Complete Scheduling"));

        expect(onSubmit).toHaveBeenCalledWith({
            date: "2026-03-10",
            time: "14:00",
            file: file
        });
    });

    it("skips upload if identity document already exists", async () => {
        const onSubmit = vi.fn().mockResolvedValue(true);
        render(VisitModal, {
            isOpen: true,
            onClose: () => {},
            property: mockProperty,
            identityDoc: { id: 1, name: "id.png" },
            onSubmit
        });

        await fireEvent.input(screen.getByLabelText("Date"), { target: { value: "2026-03-10" } });
        await fireEvent.input(screen.getByLabelText("Time"), { target: { value: "14:00" } });
        await fireEvent.click(screen.getByText("Confirm Visit"));

        expect(onSubmit).toHaveBeenCalledWith({
            date: "2026-03-10",
            time: "14:00",
            file: null
        });
    });
});
