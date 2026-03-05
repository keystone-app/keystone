import { render, screen, fireEvent } from "@testing-library/svelte";
import { describe, it, expect, vi } from "vitest";
import OfferTable from "../../components/features/OfferTable.svelte";

describe("OfferTable", () => {
    const mockOffers = [
        {
            id: 1,
            user: { name: "Tenant A" },
            property: { name: "Luxury Villa" },
            amount: 5000,
            terms: "No pets",
            status: "pending",
            compliance_status_label: "none"
        }
    ];

    it("renders empty state when no offers", () => {
        render(OfferTable, { offers: [] });
        expect(screen.getByText(/No offers found/i)).toBeTruthy();
    });

    it("renders offer list for tenant", () => {
        render(OfferTable, { offers: mockOffers, role: "tenant" });
        expect(screen.queryByText("Tenant A")).toBeNull();
        expect(screen.getByText("Luxury Villa")).toBeTruthy();
        expect(screen.getByText(/5,000/)).toBeTruthy();
        expect(screen.getByText(/Awaiting Response/i)).toBeTruthy();
    });

    it("renders offer list for landlord with actions", async () => {
        const onUpdateStatus = vi.fn();
        render(OfferTable, { offers: mockOffers, role: "landlord", onUpdateStatus });
        
        expect(screen.getByText("Tenant A")).toBeTruthy();
        
        const acceptBtn = screen.getByText("Accept");
        await fireEvent.click(acceptBtn);
        expect(onUpdateStatus).toHaveBeenCalledWith(1, "accepted");
    });

    it("shows compliance actions for tenant when accepted", async () => {
        const acceptedOffer = [{ ...mockOffers[0], status: "accepted", compliance_status_label: "awaiting_documents" }];
        const onUploadCompliance = vi.fn();
        
        const { container } = render(OfferTable, { 
            offers: acceptedOffer, 
            role: "tenant", 
            onUploadCompliance 
        });

        expect(screen.getByText(/Uploading Docs/i)).toBeTruthy();
        
        const file = new File(["test"], "income.pdf", { type: "application/pdf" });
        const inputs = container.querySelectorAll('input[type="file"]');
        await fireEvent.change(inputs[0], { target: { files: [file] } });

        expect(onUploadCompliance).toHaveBeenCalled();
    });

    it("shows verification action for tenant when pending_verification", async () => {
        const pendingOffer = [{ ...mockOffers[0], status: "accepted", compliance_status_label: "pending_verification" }];
        const onVerifyIncome = vi.fn();
        
        render(OfferTable, { 
            offers: pendingOffer, 
            role: "tenant", 
            onVerifyIncome 
        });

        expect(screen.getByText(/In Verification/i)).toBeTruthy();
        const verifyBtn = screen.getByText(/Verify Identity/i);
        await fireEvent.click(verifyBtn);
        expect(onVerifyIncome).toHaveBeenCalledWith(1);
    });

    it("shows verified status for tenant", () => {
        const verifiedOffer = [{ ...mockOffers[0], status: "accepted", compliance_status_label: "verified" }];
        render(OfferTable, { offers: verifiedOffer, role: "tenant" });
        expect(screen.getByText(/Income Verified/i)).toBeTruthy();
        expect(screen.getByText(/Ready for Lease/i)).toBeTruthy();
    });

    it("calls onUpdateStatus with countered and rejected", async () => {
        const onUpdateStatus = vi.fn();
        render(OfferTable, { offers: mockOffers, role: "landlord", onUpdateStatus });
        
        await fireEvent.click(screen.getByText("Counter"));
        expect(onUpdateStatus).toHaveBeenCalledWith(1, "countered");

        await fireEvent.click(screen.getByText("Reject"));
        expect(onUpdateStatus).toHaveBeenCalledWith(1, "rejected");
    });
});
