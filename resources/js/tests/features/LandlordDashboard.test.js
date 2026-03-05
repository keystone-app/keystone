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

    it("renders portfolio view with thumbnails", () => {
        const properties = [{ 
            id: 1, name: "Prop 1", address: "Addr 1", price: 1000, status: "available",
            media: [{ type: 'property_image', path: 'thumb.jpg' }]
        }];
        render(LandlordDashboard, { ...mockProps, properties });
        expect(screen.getByAltText("Prop 1")).toBeTruthy();
    });

    it("renders maintenance view with data", () => {
        const maintenanceRequests = [{
            id: 1, title: "Issue 1", description: "Desc 1", status: "reported", created_at: new Date(),
            lease: { property: { name: "Prop 1" } }
        }];
        render(LandlordDashboard, { ...mockProps, landlordView: 'maintenance', maintenanceRequests });
        expect(screen.getByText("Issue 1")).toBeTruthy();
        expect(screen.getByText("Desc 1")).toBeTruthy();
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

    it("calls onAddProperty when button clicked", async () => {
        const onAddProperty = vi.fn();
        const { fireEvent } = await import("@testing-library/svelte");
        render(LandlordDashboard, { ...mockProps, onAddProperty });
        await fireEvent.click(screen.getByText(/Add Property/i));
        expect(onAddProperty).toHaveBeenCalled();
    });

    it("renders empty states for all views", () => {
        render(LandlordDashboard, { ...mockProps, properties: [], landlordView: 'properties' });
        // Empty state title is "No properties found"
        expect(screen.getByRole("heading", { name: /No properties found/i })).toBeTruthy();

        render(LandlordDashboard, { ...mockProps, landlordVisits: [], landlordView: 'visits' });
        expect(screen.getByRole("heading", { name: /No visits found/i })).toBeTruthy();

        render(LandlordDashboard, { ...mockProps, offers: [], landlordView: 'offers' });
        expect(screen.getByRole("heading", { name: /No offers found/i })).toBeTruthy();

        render(LandlordDashboard, { ...mockProps, maintenanceRequests: [], landlordView: 'maintenance' });
        expect(screen.getByRole("heading", { name: /No maintenance requests/i })).toBeTruthy();
    });

    it("renders data tables for visits and offers", () => {
        const landlordVisits = [{ id: 1, visit_at: new Date(), status: 'pending', user: { name: 'T' }, property: { name: 'P' } }];
        render(LandlordDashboard, { ...mockProps, landlordView: 'visits', landlordVisits });
        expect(screen.getAllByRole("table").length).toBeGreaterThan(0);

        const offers = [{ id: 1, amount: 1000, status: 'pending', user: { name: 'T' }, property: { name: 'P' } }];
        render(LandlordDashboard, { ...mockProps, landlordView: 'offers', offers });
        expect(screen.getAllByRole("table").length).toBeGreaterThan(0);
    });
});
