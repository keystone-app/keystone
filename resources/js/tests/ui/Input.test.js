import { render, screen, fireEvent } from "@testing-library/svelte";
import { describe, it, expect, vi } from "vitest";
import Input from "../../components/ui/Input.svelte";

describe("Input Component", () => {
    it("renders with a label and placeholder", () => {
        render(Input, { 
            id: "test-input", 
            label: "Test Label", 
            placeholder: "Test Placeholder" 
        });

        expect(screen.getByText("Test Label")).toBeTruthy();
        expect(screen.getByPlaceholderText("Test Placeholder")).toBeTruthy();
    });

    it("binds the value correctly", async () => {
        const { component } = render(Input, { 
            id: "test-input", 
            value: "initial"
        });

        const input = screen.getByRole("textbox");
        expect(input.value).toBe("initial");

        await fireEvent.input(input, { target: { value: "updated" } });
        expect(input.value).toBe("updated");
    });

    it("applies required attribute", () => {
        render(Input, { id: "test-input", required: true });
        const input = screen.getByRole("textbox");
        expect(input.hasAttribute("required")).toBe(true);
    });
});
