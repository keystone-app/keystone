import { render, screen, fireEvent } from "@testing-library/svelte";
import { describe, it, expect, vi } from "vitest";
import ListingsView from "../../components/features/ListingsView.svelte";

describe("ListingsView", () => {
    const mockProperties = [
        { id: 1, name: "Modern Loft", address: "123 St", price: 1000, status: "available", type: "Apartment" },
        { id: 2, name: "Cozy Studio", address: "456 Ave", price: 800, status: "rented", type: "Studio" }
    ];

    it("renders all provided properties", () => {
        render(ListingsView, { properties: mockProperties, onPropertySelect: () => {} });
        expect(screen.getByText("Modern Loft")).toBeTruthy();
        expect(screen.getByText("Cozy Studio")).toBeTruthy();
    });

    it("filters properties by search query", async () => {
        render(ListingsView, { properties: mockProperties, onPropertySelect: () => {} });
        
        const searchInput = screen.getByPlaceholderText(/search by property name/i);
        await fireEvent.input(searchInput, { target: { value: "Loft" } });

        expect(screen.queryByText("Modern Loft")).toBeTruthy();
        expect(screen.queryByText("Cozy Studio")).toBeNull();
    });

    it("filters properties by status", async () => {
        render(ListingsView, { properties: mockProperties, onPropertySelect: () => {} });
        
        const statusSelect = screen.getByLabelText("Filter by status");
        await fireEvent.change(statusSelect, { target: { value: "available" } });

        expect(screen.queryByText("Modern Loft")).toBeTruthy();
        expect(screen.queryByText("Cozy Studio")).toBeNull();
    });
});
