# Specification: Tenant Compliance and Verification Flow

## Goal
Implement a robust, automated workflow for tenant compliance and document verification after an offer has been accepted by a landlord. This flow bridges the "Negotiation" and "Legal" domains.

## Core Flow
1. **Offer Accepted:** Landlord accepts an offer -> Offer transitions to `AwaitingDocuments`.
2. **Document Upload:** Tenant uploads required compliance documents (Income Proof, Residency Proof).
3. **Transition to Verification:** Once all required documents are uploaded, the offer automatically transitions to `PendingVerification`.
4. **Income Verification:** Tenant triggers the "Verify Income" action (currently a mock for Brazil Open Finance).
5. **Verified Status:** Upon successful verification, the offer transitions to `Verified`.
6. **Lease Draft Generation:** The system automatically triggers the creation of a `Lease` draft once the offer is `Verified`.

## Technical Requirements
- **Domains Involved:** `Negotiation`, `Legal`, `Identity`.
- **States:** Update and ensure correct transitions for `OfferStatus`.
- **Actions:** 
  - Enhance `RespondToOfferAction` to initiate the compliance flow.
  - Implement/Refine `UploadComplianceDocumentAction`.
  - Implement/Refine `VerifyIncomeAction`.
  - Automate `CreateLeaseFromOfferAction` triggering.
- **Testing:** 100% test coverage for all new and modified code.
- **UI:** Update `TenantDashboard` to show document upload status and verification actions.

## Acceptance Criteria
- [ ] Landlord accepting an offer correctly moves it to `AwaitingDocuments`.
- [ ] Tenant can upload all required documents.
- [ ] Offer status automatically moves to `PendingVerification` only after all docs are present.
- [ ] Income verification transitions the offer to `Verified`.
- [ ] A `Lease` draft is automatically created when the offer is `Verified`.
- [ ] UI reflects all status changes and provides clear feedback.
