import { render, screen, fireEvent } from "@testing-library/svelte";
import { describe, it, expect, vi } from "vitest";
import MaintenanceRequestModal from "../../components/features/MaintenanceRequestModal.svelte";

describe("MaintenanceRequestModal", () => {
    const mockLease = { id: 1, property: { name: "Test Property" } };

    it("renders the modal when open", () => {
        render(MaintenanceRequestModal, {
            isOpen: true,
            onClose: () => {},
            lease: mockLease,
            onSubmit: () => {}
        });

        expect(screen.getByText("Report Maintenance Issue")).toBeTruthy();
        expect(screen.getByText(/Test Property/)).toBeTruthy();
        expect(screen.getByLabelText("Issue Title")).toBeTruthy();
        expect(screen.getByLabelText("Description")).toBeTruthy();
    });

    it("does not render when closed", () => {
        render(MaintenanceRequestModal, {
            isOpen: false,
            onClose: () => {},
            lease: mockLease,
            onSubmit: () => {}
        });

        expect(screen.queryByText("Report Maintenance Issue")).toBeNull();
    });

    it("submits the form with correct data", async () => {
        const onSubmit = vi.fn().mockResolvedValue(true);
        render(MaintenanceRequestModal, {
            isOpen: true,
            onClose: () => {},
            lease: mockLease,
            onSubmit: onSubmit
        });

        await fireEvent.input(screen.getByLabelText("Issue Title"), { target: { value: "Leaking tap" } });
        await fireEvent.input(screen.getByLabelText("Description"), { target: { value: "Kitchen tap is dripping" } });

        const submitButton = screen.getByText("Submit Request");
        await fireEvent.click(submitButton);

        expect(onSubmit).toHaveBeenCalledWith({
            lease_id: 1,
            title: "Leaking tap",
            description: "Kitchen tap is dripping"
        });
    });

    it("requires a title for submission", async () => {
        const onSubmit = vi.fn();
        render(MaintenanceRequestModal, {
            isOpen: true,
            onClose: () => {},
            lease: mockLease,
            onSubmit: onSubmit
        });

        const submitButton = screen.getByText("Submit Request");
        await fireEvent.click(submitButton);

        expect(onSubmit).not.toHaveBeenCalled();
    });
});
