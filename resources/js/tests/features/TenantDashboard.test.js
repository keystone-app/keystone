import { render, screen } from "@testing-library/svelte";
import { describe, it, expect } from "vitest";
import TenantDashboard from "../../components/features/TenantDashboard.svelte";

describe("TenantDashboard", () => {
    const mockProps = {
        tenantView: 'leases',
        myVisits: [],
        offers: [],
        identityDoc: null,
        onMakeOffer: () => {},
        onUploadCompliance: () => {},
        onVerifyIncome: () => {}
    };

    it("renders leases view by default", () => {
        render(TenantDashboard, mockProps);
        expect(screen.getByText("My Leases")).toBeTruthy();
        expect(screen.getAllByText(/Active Lease/i).length).toBeGreaterThan(0);
    });

    it("renders visits view when tenantView is visits", () => {
        render(TenantDashboard, { ...mockProps, tenantView: 'visits' });
        expect(screen.getByText("My Scheduled Visits")).toBeTruthy();
    });

    it("renders offers view when tenantView is offers", () => {
        render(TenantDashboard, { ...mockProps, tenantView: 'offers' });
        expect(screen.getByText("Negotiations")).toBeTruthy();
    });

    it("renders payments view when tenantView is payments", () => {
        render(TenantDashboard, { ...mockProps, tenantView: 'payments' });
        expect(screen.getByText("Payments")).toBeTruthy();
    });

    it("renders maintenance view when tenantView is maintenance", () => {
        render(TenantDashboard, { ...mockProps, tenantView: 'maintenance' });
        expect(screen.getByText("Maintenance Requests")).toBeTruthy();
    });

    it("renders a list of maintenance requests", () => {
        const maintenanceRequests = [
            {
                id: 1,
                title: "Leaking tap",
                status: "reported",
                created_at: "2026-03-05T10:00:00Z",
                lease: { property: { name: "Test Loft" } }
            }
        ];
        render(TenantDashboard, { ...mockProps, tenantView: 'maintenance', maintenanceRequests });
        expect(screen.getByText("Leaking tap")).toBeTruthy();
        expect(screen.getByText("Test Loft")).toBeTruthy();
        expect(screen.getByText(/reported/i)).toBeTruthy();
    });

    it("renders leases view with data and report action", async () => {
        const myLeases = [{ id: 1, rent_amount: 1000, property: { name: "Lease Prop", address: "Addr" }, landlord: { name: "L" } }];
        const onReportMaintenance = vi.fn();
        const { fireEvent } = await import("@testing-library/svelte");
        
        render(TenantDashboard, { ...mockProps, myLeases, onReportMaintenance });
        expect(screen.getByText(/Active Lease: Lease Prop/i)).toBeTruthy();
        
        await fireEvent.click(screen.getByText(/Report Issue/i));
        expect(onReportMaintenance).toHaveBeenCalledWith(myLeases[0]);
    });

    it("renders empty leases view", () => {
        render(TenantDashboard, { ...mockProps, myLeases: [] });
        expect(screen.getByText(/No Active Leases/i)).toBeTruthy();
    });

    it("renders visits and offers tables with various statuses", () => {
        const myVisits = [{ id: 1, visit_at: new Date(), status: 'pending', property: { name: 'P' } }];
        render(TenantDashboard, { ...mockProps, tenantView: 'visits', myVisits });
        expect(screen.getAllByRole("table").length).toBeGreaterThan(0);

        const offers = [
            { id: 1, amount: 1000, status: 'accepted', compliance_status_label: 'verified', property: { name: 'P' }, user: { name: 'U' } },
            { id: 2, amount: 2000, status: 'accepted', compliance_status_label: 'pending_verification', property: { name: 'P' }, user: { name: 'U' } },
            { id: 3, amount: 3000, status: 'pending', compliance_status_label: 'none', property: { name: 'P' }, user: { name: 'U' } }
        ];
        render(TenantDashboard, { ...mockProps, tenantView: 'offers', offers });
        expect(screen.getByText(/Income Verified/i)).toBeTruthy();
        expect(screen.getByText(/In Verification/i)).toBeTruthy();
        expect(screen.getByText(/N\/A/i)).toBeTruthy();
    });
});
