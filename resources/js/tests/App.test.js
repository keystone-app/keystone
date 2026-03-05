import { render, screen, waitFor } from "@testing-library/svelte";
import { describe, it, expect, vi, beforeEach } from "vitest";
import App from "../App.svelte";

// Mocking fetch for auth checks
global.fetch = vi.fn();

describe("App Layout", () => {
	beforeEach(() => {
		vi.clearAllMocks();
	});

	it("does not render the sidebar when the user is unauthenticated", async () => {
		// Mock responses for auth and properties
		fetch.mockImplementation((url) => {
			if (url === '/me') {
				return Promise.resolve({
					ok: true,
					json: () => Promise.resolve({ user: null })
				});
			}
			if (url === '/properties') {
				return Promise.resolve({
					ok: true,
					json: () => Promise.resolve([])
				});
			}
			return Promise.reject(new Error('Unknown URL'));
		});

		render(App);
		
		// The sidebar is an <aside> element with role "complementary"
		await waitFor(() => {
			expect(screen.queryByRole("complementary")).toBeNull();
		});
	});

	it("renders the sidebar when the user is authenticated", async () => {
		// Mock responses for auth and properties
		fetch.mockImplementation((url) => {
			if (url === '/me') {
				return Promise.resolve({
					ok: true,
					json: () => Promise.resolve({ 
						user: { name: "Test User" },
						role: "tenant",
						identity_document: null
					})
				});
			}
			if (url === '/properties') {
				return Promise.resolve({
					ok: true,
					json: () => Promise.resolve([])
				});
			}
			return Promise.resolve({ ok: true, json: () => Promise.resolve([]) });
		});

		render(App);
		
		// The sidebar should eventually appear
		const sidebar = await screen.findByRole("complementary");
		expect(sidebar).toBeTruthy();
	});
});
