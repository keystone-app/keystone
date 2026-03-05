import { render, screen, fireEvent } from "@testing-library/svelte";
import { describe, it, expect, vi } from "vitest";
import ListingsView from "../../components/features/ListingsView.svelte";

describe("ListingsView", () => {
    const mockProperties = [
        { id: 1, name: "Modern Loft", address: "123 St", price: 1000, status: "available", type: "Apartment" },
        { id: 2, name: "Cozy Studio", address: "456 Ave", price: 800, status: "rented", type: "Studio" }
    ];
    const mockFilters = { min_price: "", max_price: "", type: "", status: "" };

    it("renders all provided properties", () => {
        render(ListingsView, { 
            properties: mockProperties, 
            filters: mockFilters,
            onFilterChange: () => {},
            onPropertySelect: () => {} 
        });
        expect(screen.getByText("Modern Loft")).toBeTruthy();
        expect(screen.getByText("Cozy Studio")).toBeTruthy();
    });

    it("filters properties by search query (client-side)", async () => {
        render(ListingsView, { 
            properties: mockProperties, 
            filters: mockFilters,
            onFilterChange: () => {},
            onPropertySelect: () => {} 
        });
        
        const searchInput = screen.getByPlaceholderText(/search by property name/i);
        await fireEvent.input(searchInput, { target: { value: "Loft" } });

        expect(screen.queryByText("Modern Loft")).toBeTruthy();
        expect(screen.queryByText("Cozy Studio")).toBeNull();
    });

    it("renders PropertyFilters and passes props", async () => {
        const onFilterChange = vi.fn();
        render(ListingsView, { 
            properties: mockProperties, 
            filters: mockFilters,
            onFilterChange,
            onPropertySelect: () => {} 
        });

        expect(screen.getByLabelText(/Min Price/i)).toBeTruthy();
        
        const minPriceInput = screen.getByLabelText(/Min Price/i);
        await fireEvent.input(minPriceInput, { target: { value: "1000" } });

        expect(onFilterChange).toHaveBeenCalledWith(expect.objectContaining({
            min_price: "1000"
        }));
    });
});
