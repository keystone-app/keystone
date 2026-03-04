import { render, screen, fireEvent } from "@testing-library/svelte";
import { describe, it, expect, vi } from "vitest";
import PropertyModal from "../../components/features/PropertyModal.svelte";

describe("PropertyModal", () => {
    it("renders all property fields", () => {
        render(PropertyModal, {
            isOpen: true,
            onClose: () => {},
            onSubmit: () => {}
        });

        expect(screen.getByLabelText("Property Name")).toBeTruthy();
        expect(screen.getByLabelText("Address")).toBeTruthy();
        expect(screen.getByLabelText("Monthly Rent ($)")).toBeTruthy();
        expect(screen.getByLabelText("Property Type")).toBeTruthy();
        expect(screen.getByLabelText("Description")).toBeTruthy();
    });

    it("submits form data correctly", async () => {
        const handleSubmit = vi.fn(() => Promise.resolve(true));
        render(PropertyModal, {
            isOpen: true,
            onClose: () => {},
            onSubmit: handleSubmit
        });

        await fireEvent.input(screen.getByLabelText("Property Name"), { target: { value: "New Villa" } });
        await fireEvent.input(screen.getByLabelText("Address"), { target: { value: "123 Villa St" } });
        await fireEvent.input(screen.getByLabelText("Monthly Rent ($)"), { target: { value: "3000" } });
        
        const submitButton = screen.getByText("List Property");
        await fireEvent.click(submitButton);

        expect(handleSubmit).toHaveBeenCalledWith({
            name: "New Villa",
            address: "123 Villa St",
            price: 3000,
            type: "Apartment",
            description: ""
        });
    });
});
