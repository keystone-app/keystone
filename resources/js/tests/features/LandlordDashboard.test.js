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

    it("renders maintenance view when landlordView is maintenance", () => {
        render(LandlordDashboard, { ...mockProps, landlordView: 'maintenance' });
        expect(screen.getByText("Maintenance Management")).toBeTruthy();
    });

    it("renders a list of maintenance requests for landlords", () => {
        const maintenanceRequests = [
            {
                id: 1,
                title: "Broken window",
                status: "reported",
                created_at: "2026-03-05T10:00:00Z",
                lease: { property: { name: "Test Villa" } }
            }
        ];
        render(LandlordDashboard, { ...mockProps, landlordView: 'maintenance', maintenanceRequests });
        expect(screen.getByText("Broken window")).toBeTruthy();
        expect(screen.getByText("Test Villa")).toBeTruthy();
        expect(screen.getByText(/reported/i)).toBeTruthy();
    });

    it("calls onUpdateMaintenanceStatus when status is changed", async () => {
        const onUpdateMaintenanceStatus = vi.fn();
        const maintenanceRequests = [
            {
                id: 1,
                title: "Broken window",
                status: "reported",
                created_at: "2026-03-05T10:00:00Z",
                lease: { property: { name: "Test Villa" } }
            }
        ];
        const { fireEvent } = await import("@testing-library/svelte");
        render(LandlordDashboard, { 
            ...mockProps, 
            landlordView: 'maintenance', 
            maintenanceRequests,
            onUpdateMaintenanceStatus
        });

        const startButton = screen.getByText("Start Work");
        await fireEvent.click(startButton);

        expect(onUpdateMaintenanceStatus).toHaveBeenCalledWith(1, 'in_progress');
    });
});
