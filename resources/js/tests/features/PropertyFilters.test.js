import { render, screen, fireEvent } from "@testing-library/svelte";
import { describe, it, expect, vi } from "vitest";
import PropertyFilters from "../../components/features/PropertyFilters.svelte";

describe("PropertyFilters", () => {
    it("renders all filter inputs", () => {
        render(PropertyFilters, { filters: {}, onFilterChange: () => {} });

        expect(screen.getByLabelText(/Min Price/i)).toBeTruthy();
        expect(screen.getByLabelText(/Max Price/i)).toBeTruthy();
        expect(screen.getByLabelText(/Property Type/i)).toBeTruthy();
        expect(screen.getByLabelText(/Status/i)).toBeTruthy();
    });

    it("emits onFilterChange when an input changes", async () => {
        const onFilterChange = vi.fn();
        render(PropertyFilters, { 
            filters: { min_price: "", max_price: "", type: "", status: "" }, 
            onFilterChange 
        });

        const minPriceInput = screen.getByLabelText(/Min Price/i);
        await fireEvent.input(minPriceInput, { target: { value: "1000" } });

        expect(onFilterChange).toHaveBeenCalledWith(expect.objectContaining({
            min_price: "1000"
        }));
    });

    it("resets all filters when reset button is clicked", async () => {
        const onFilterChange = vi.fn();
        render(PropertyFilters, { 
            filters: { min_price: "1000", max_price: "5000", type: "apartment", status: "available" }, 
            onFilterChange 
        });

        const resetButton = screen.getByText(/Reset Filters/i);
        await fireEvent.click(resetButton);

        expect(onFilterChange).toHaveBeenCalledWith({
            min_price: "",
            max_price: "",
            type: "",
            status: ""
        });
    });
});
