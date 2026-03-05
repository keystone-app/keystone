import { render, screen, fireEvent } from "@testing-library/svelte";
import { describe, it, expect, vi } from "vitest";
import VisitTable from "../../components/features/VisitTable.svelte";

describe("VisitTable", () => {
    const mockVisits = [
        {
            id: 1,
            user: { name: "Tenant A" },
            property: { name: "Luxury Villa" },
            visit_at: "2026-03-10T14:00:00Z",
            status: "pending"
        }
    ];

    it("renders empty state when no visits", () => {
        render(VisitTable, { visits: [] });
        expect(screen.getByText(/No visits found/i)).toBeTruthy();
    });

    it("renders visit list for landlord with approve action", async () => {
        const onApprove = vi.fn();
        const onReject = vi.fn();
        render(VisitTable, { visits: mockVisits, role: "landlord", onApprove, onReject });
        
        expect(screen.getByText("Tenant A")).toBeTruthy();
        
        const approveBtn = screen.getByText("Approve");
        await fireEvent.click(approveBtn);
        expect(onApprove).toHaveBeenCalledWith(1);

        const rejectBtn = screen.getByText("Reject");
        await fireEvent.click(rejectBtn);
        expect(onReject).toHaveBeenCalledWith(1);
    });

    it("renders visit list for tenant with cancel action", async () => {
        render(VisitTable, { visits: mockVisits, role: "tenant" });
        const cancelBtn = screen.getByText("Cancel");
        expect(cancelBtn).toBeTruthy();
    });

    it("calls onViewId when link clicked", async () => {
        const onViewId = vi.fn();
        render(VisitTable, { visits: mockVisits, role: "landlord", onViewId });
        
        const viewIdBtn = screen.getByText(/View ID Document/i);
        await fireEvent.click(viewIdBtn);
        expect(onViewId).toHaveBeenCalledWith(mockVisits[0]);
    });
});
