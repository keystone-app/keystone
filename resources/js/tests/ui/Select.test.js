import { render, screen, fireEvent } from "@testing-library/svelte";
import { describe, it, expect } from "vitest";
import Select from "../../components/ui/Select.svelte";

describe("Select Component", () => {
    const options = [
        { value: "apt", label: "Apartment" },
        { value: "hse", label: "House" }
    ];

    it("renders with a label and options", () => {
        render(Select, { 
            id: "test-select", 
            label: "Category", 
            options 
        });

        expect(screen.getByText("Category")).toBeTruthy();
        expect(screen.getByText("Apartment")).toBeTruthy();
        expect(screen.getByText("House")).toBeTruthy();
    });

    it("binds the value correctly", async () => {
        render(Select, { 
            id: "test-select", 
            options,
            value: "apt"
        });

        const select = screen.getByRole("combobox");
        expect(select.value).toBe("apt");

        await fireEvent.change(select, { target: { value: "hse" } });
        expect(select.value).toBe("hse");
    });

    it("supports simple string options", () => {
        render(Select, { 
            id: "test-select", 
            options: ["A", "B"],
            value: "A"
        });

        expect(screen.getByText("A")).toBeTruthy();
        expect(screen.getByText("B")).toBeTruthy();
    });

    it("renders with default empty options", () => {
        const { container } = render(Select, { id: "test-select" });
        const select = container.querySelector("select");
        expect(select.children.length).toBe(0);
    });
});
