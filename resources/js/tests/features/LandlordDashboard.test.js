import { render, screen } from "@testing-library/svelte";
import { describe, it, expect } from "vitest";
import LandlordDashboard from "../../components/features/LandlordDashboard.svelte";

describe("LandlordDashboard", () => {
    const mockProps = {
        landlordView: 'properties',
        properties: [{ id: 1, name: "Prop 1", address: "Addr 1", price: 1000, status: "available" }],
        landlordVisits: [],
        offers: [],
        onAddProperty: () => {},
        onApproveVisit: () => {},
        onRejectVisit: () => {},
        onUpdateOfferStatus: () => {}
    };

    it("renders portfolio view when landlordView is properties", () => {
        render(LandlordDashboard, mockProps);
        expect(screen.getByText("Properties")).toBeTruthy();
        expect(screen.getByText("Prop 1")).toBeTruthy();
    });

    it("renders visits view when landlordView is visits", () => {
        render(LandlordDashboard, { ...mockProps, landlordView: 'visits' });
        expect(screen.getByText("Visit Requests")).toBeTruthy();
    });

    it("renders offers view when landlordView is offers", () => {
        render(LandlordDashboard, { ...mockProps, landlordView: 'offers' });
        expect(screen.getByText("Offer Negotiations")).toBeTruthy();
    });
});
