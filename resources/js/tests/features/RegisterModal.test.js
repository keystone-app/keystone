import { render, screen, fireEvent } from "@testing-library/svelte";
import { describe, it, expect, vi } from "vitest";
import RegisterModal from "../../components/features/RegisterModal.svelte";

describe("RegisterModal", () => {
    it("renders when open", () => {
        render(RegisterModal, { isOpen: true, onClose: () => {}, onRegister: () => {}, onToggleLogin: () => {} });
        expect(screen.getByRole("heading", { name: /Create Account/i })).toBeTruthy();
        expect(screen.getByLabelText(/Full Name/i)).toBeTruthy();
        expect(screen.getByLabelText(/Email Address/i)).toBeTruthy();
        expect(screen.getByLabelText(/Password/i)).toBeTruthy();
    });

    it("displays error message", () => {
        render(RegisterModal, { 
            isOpen: true, 
            onClose: () => {}, 
            onRegister: () => {}, 
            onToggleLogin: () => {},
            error: "Registration failed" 
        });
        expect(screen.getByText("Registration failed")).toBeTruthy();
    });

    it("shows submitting state", () => {
        render(RegisterModal, { 
            isOpen: true, 
            onClose: () => {}, 
            onRegister: () => {}, 
            onToggleLogin: () => {},
            isSubmitting: true 
        });
        expect(screen.getByText(/Creating account.../i)).toBeTruthy();
        expect(screen.getByRole("button", { name: /Creating account.../i }).disabled).toBe(true);
    });

    it("calls onRegister with form data", async () => {
        const onRegister = vi.fn();
        render(RegisterModal, { 
            isOpen: true, 
            onClose: () => {}, 
            onRegister, 
            onToggleLogin: () => {} 
        });

        await fireEvent.input(screen.getByLabelText(/Full Name/i), { target: { value: "John Doe" } });
        await fireEvent.input(screen.getByLabelText(/Email Address/i), { target: { value: "john@example.com" } });
        await fireEvent.input(screen.getByLabelText(/Password/i), { target: { value: "password123" } });
        
        await fireEvent.submit(screen.getByRole("button", { name: /Create Account/i }));

        expect(onRegister).toHaveBeenCalledWith({
            name: "John Doe",
            email: "john@example.com",
            password: "password123"
        });
    });

    it("calls onToggleLogin when link clicked", async () => {
        const onToggleLogin = vi.fn();
        render(RegisterModal, { 
            isOpen: true, 
            onClose: () => {}, 
            onRegister: () => {}, 
            onToggleLogin 
        });

        await fireEvent.click(screen.getByText(/Sign in/i));
        expect(onToggleLogin).toHaveBeenCalled();
    });
});
