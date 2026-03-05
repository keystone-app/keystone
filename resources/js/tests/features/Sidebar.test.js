import { render, screen, fireEvent } from "@testing-library/svelte";
import { describe, it, expect, vi } from "vitest";
import Sidebar from "../../components/features/Sidebar.svelte";

describe("Sidebar", () => {
    const mockProps = {
        role: "guest",
        currentView: "listings",
        onViewChange: vi.fn(),
        onLandlordViewChange: vi.fn(),
        onTenantViewChange: vi.fn(),
        onLogin: vi.fn(),
        onRegister: vi.fn(),
        onLogout: vi.fn(),
        isLoggedIn: false
    };

    it("renders guest view", () => {
        render(Sidebar, { ...mockProps });
        expect(screen.getByRole("button", { name: /Sign In/i })).toBeTruthy();
        expect(screen.getByRole("button", { name: /Create Account/i })).toBeTruthy();
    });

    it("renders tenant dashboard navigation", async () => {
        render(Sidebar, { 
            ...mockProps, 
            role: "tenant", 
            currentUser: { name: "Tenant User" },
            isLoggedIn: true, 
            currentView: "dashboard",
            tenantView: "visits"
        });

        await fireEvent.click(screen.getByText(/Browse Properties/i));
        expect(mockProps.onViewChange).toHaveBeenCalledWith("listings");

        await fireEvent.click(screen.getByText(/My Dashboard/i));
        expect(mockProps.onViewChange).toHaveBeenCalledWith("dashboard");

        await fireEvent.click(screen.getByText(/Scheduled Visits/i));
        expect(mockProps.onTenantViewChange).toHaveBeenCalledWith("visits");

        await fireEvent.click(screen.getByText(/Negotiations/i));
        expect(mockProps.onTenantViewChange).toHaveBeenCalledWith("offers");

        await fireEvent.click(screen.getByText(/My Leases/i));
        expect(mockProps.onTenantViewChange).toHaveBeenCalledWith("leases");

        await fireEvent.click(screen.getByText(/Maintenance/i));
        expect(mockProps.onTenantViewChange).toHaveBeenCalledWith("maintenance");
    });

    it("renders landlord dashboard navigation", async () => {
        render(Sidebar, { 
            ...mockProps, 
            role: "landlord", 
            currentUser: { name: "Landlord User" },
            isLoggedIn: true, 
            currentView: "dashboard",
            landlordView: "properties"
        });

        await fireEvent.click(screen.getByText(/My Portfolio/i));
        expect(mockProps.onLandlordViewChange).toHaveBeenCalledWith("properties");

        await fireEvent.click(screen.getByText(/Visit Requests/i));
        expect(mockProps.onLandlordViewChange).toHaveBeenCalledWith("visits");

        await fireEvent.click(screen.getByText(/Offers/i));
        expect(mockProps.onLandlordViewChange).toHaveBeenCalledWith("offers");

        await fireEvent.click(screen.getByText(/Maintenance/i));
        expect(mockProps.onLandlordViewChange).toHaveBeenCalledWith("maintenance");
    });

    it("calls onLogin and onRegister when guest clicks buttons", async () => {
        render(Sidebar, { ...mockProps });
        await fireEvent.click(screen.getByRole("button", { name: /Sign In/i }));
        expect(mockProps.onLogin).toHaveBeenCalled();

        await fireEvent.click(screen.getByRole("button", { name: /Create Account/i }));
        expect(mockProps.onRegister).toHaveBeenCalled();
    });

    it("shows badges for pending items", () => {
        render(Sidebar, { 
            ...mockProps, 
            role: "landlord", 
            currentUser: { name: "Landlord User" },
            isLoggedIn: true, 
            currentView: "dashboard",
            landlordView: "properties",
            landlordVisits: [{ status: "pending" }],
            offers: [{ status: "pending" }],
            maintenanceRequests: [{ status: "reported" }]
        });

        // Check for badge text (numbers)
        expect(screen.getAllByText("1").length).toBeGreaterThan(0);
    });

    it("calls onLogout when logout button clicked", async () => {
        render(Sidebar, { ...mockProps, isLoggedIn: true, currentUser: { name: "User" } });
        await fireEvent.click(screen.getByLabelText(/Logout/i));
        expect(mockProps.onLogout).toHaveBeenCalled();
    });
});
