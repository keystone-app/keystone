import { render, screen, fireEvent } from "@testing-library/svelte";
import { describe, it, expect } from "vitest";
import TextArea from "../../components/ui/TextArea.svelte";

describe("TextArea Component", () => {
    it("renders with a label and placeholder", () => {
        render(TextArea, { 
            id: "test-textarea", 
            label: "Description", 
            placeholder: "Enter text" 
        });

        expect(screen.getByText("Description")).toBeTruthy();
        expect(screen.getByPlaceholderText("Enter text")).toBeTruthy();
    });

    it("binds the value correctly", async () => {
        render(TextArea, { 
            id: "test-textarea", 
            value: "hello"
        });

        const textarea = screen.getByRole("textbox");
        expect(textarea.value).toBe("hello");

        await fireEvent.input(textarea, { target: { value: "world" } });
        expect(textarea.value).toBe("world");
    });

    it("respects rows prop", () => {
        render(TextArea, { id: "test-textarea", rows: 10 });
        const textarea = screen.getByRole("textbox");
        expect(textarea.getAttribute("rows")).toBe("10");
    });
});
