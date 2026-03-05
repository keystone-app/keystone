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
            description: "",
            images: [],
            videos: []
        });
    });

    it("handles image upload and removal", async () => {
        const handleSubmit = vi.fn(() => Promise.resolve(true));
        render(PropertyModal, {
            isOpen: true,
            onClose: () => {},
            onSubmit: handleSubmit
        });

        await fireEvent.input(screen.getByLabelText("Property Name"), { target: { value: "New Villa" } });
        await fireEvent.input(screen.getByLabelText("Address"), { target: { value: "123 Villa St" } });
        await fireEvent.input(screen.getByLabelText("Monthly Rent ($)"), { target: { value: "3000" } });

        const imageInput = document.getElementById('prop-images');
        const file = new File(["test"], "test.png", { type: "image/png" });
        
        await fireEvent.change(imageInput, { target: { files: [file] } });
        
        // Wait for preview
        const preview = await screen.findByAltText("Preview");
        expect(preview).toBeTruthy();

        // Remove image
        const removeButtons = screen.getAllByRole("button");
        // Image remove buttons are top-1 right-1 with lucide-x
        const imgRemoveBtn = removeButtons.find(b => b.className.includes('absolute'));
        await fireEvent.click(imgRemoveBtn);

        expect(screen.queryByAltText("Preview")).toBeNull();
    });

    it("handles video upload and removal", async () => {
        const handleSubmit = vi.fn(() => Promise.resolve(true));
        render(PropertyModal, {
            isOpen: true,
            onClose: () => {},
            onSubmit: handleSubmit
        });

        const videoInput = document.getElementById('prop-videos');
        const file = new File(["test"], "test.mp4", { type: "video/mp4" });
        
        await fireEvent.change(videoInput, { target: { files: [file] } });
        
        expect(screen.getByText("test.mp4")).toBeTruthy();

        // Remove video
        const removeBtn = screen.getByRole("button", { name: "" }); // Find by lucide-x or text
        // The component uses <X size={14} /> inside button
        const removeButtons = screen.getAllByRole("button");
        const videoRemoveBtn = removeButtons.find(b => b.querySelector('svg'));
        await fireEvent.click(videoRemoveBtn);

        expect(screen.queryByText("test.mp4")).toBeNull();
    });
});
