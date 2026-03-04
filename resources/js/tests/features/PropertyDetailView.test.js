import { render, screen, fireEvent } from "@testing-library/svelte";
import { describe, it, expect, vi } from "vitest";
import PropertyDetailView from "../../components/features/PropertyDetailView.svelte";

describe("PropertyDetailView", () => {
    const mockProperty = { 
        id: 1, 
        name: "Luxury Villa", 
        address: "123 High St", 
        price: 5000, 
        status: "available", 
        type: "House",
        description: "A great house",
        compliance: { gas: "Verified" }
    };

    it("renders property details correctly", () => {
        render(PropertyDetailView, { 
            property: mockProperty, 
            onBack: () => {}, 
            onScheduleVisit: () => {} 
        });

        expect(screen.getByText("Luxury Villa")).toBeTruthy();
        expect(screen.getByText("123 High St")).toBeTruthy();
        expect(screen.getByText("$5,000")).toBeTruthy();
    });

    it("calls onScheduleVisit when button is clicked", async () => {
        const handleSchedule = vi.fn();
        render(PropertyDetailView, { 
            property: mockProperty, 
            onBack: () => {}, 
            onScheduleVisit: handleSchedule 
        });

        const btn = screen.getByText("Schedule a Visit");
        await fireEvent.click(btn);
        expect(handleSchedule).toHaveBeenCalled();
    });
});
