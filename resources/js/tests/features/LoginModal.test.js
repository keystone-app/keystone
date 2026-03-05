import { render, screen, fireEvent } from "@testing-library/svelte";
import { describe, it, expect, vi } from "vitest";
import LoginModal from "../../components/features/LoginModal.svelte";

describe("LoginModal", () => {
    it("renders when open", () => {
        render(LoginModal, { isOpen: true, onClose: () => {}, onLogin: () => {}, onToggleRegister: () => {} });
        expect(screen.getByText("Welcome back")).toBeTruthy();
        expect(screen.getByLabelText(/Email Address/i)).toBeTruthy();
        expect(screen.getByLabelText(/Password/i)).toBeTruthy();
    });

    it("displays error message", () => {
        render(LoginModal, { 
            isOpen: true, 
            onClose: () => {}, 
            onLogin: () => {}, 
            onToggleRegister: () => {},
            error: "Invalid credentials" 
        });
        expect(screen.getByText("Invalid credentials")).toBeTruthy();
    });

    it("calls onLogin with form data", async () => {
        const onLogin = vi.fn();
        render(LoginModal, { 
            isOpen: true, 
            onClose: () => {}, 
            onLogin, 
            onToggleRegister: () => {} 
        });

        const emailInput = screen.getByLabelText(/Email Address/i);
        const passwordInput = screen.getByLabelText(/Password/i);
        
        await fireEvent.input(emailInput, { target: { value: "test@example.com" } });
        await fireEvent.input(passwordInput, { target: { value: "password123" } });
        
        await fireEvent.submit(screen.getByRole("button", { name: /Sign In/i }));

        expect(onLogin).toHaveBeenCalledWith({
            email: "test@example.com",
            password: "password123"
        });
    });

    it("calls onToggleRegister when link clicked", async () => {
        const onToggleRegister = vi.fn();
        render(LoginModal, { 
            isOpen: true, 
            onClose: () => {}, 
            onLogin: () => {}, 
            onToggleRegister 
        });

        await fireEvent.click(screen.getByText(/Create one/i));
        expect(onToggleRegister).toHaveBeenCalled();
    });

    it("shows submitting state", () => {
        render(LoginModal, { 
            isOpen: true, 
            onClose: () => {}, 
            onLogin: () => {}, 
            onToggleRegister: () => {},
            isSubmitting: true 
        });
        expect(screen.getByText(/Signing in.../i)).toBeTruthy();
        expect(screen.getByRole("button", { name: /Signing in.../i }).disabled).toBe(true);
    });
});
