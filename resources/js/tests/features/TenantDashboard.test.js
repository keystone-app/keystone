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
});
