# Billing sandbox

A new company can publish in free mode. To try metered posting, open **Billing** and select **Add 10 demo credits**. This activates the credit workflow for that company. Each new publication costs one credit. Editing and closing an existing advert do not consume credits.

The ledger records top-ups and publication debits. A publication, its debit and its outbound event are committed in the same transaction. If there are insufficient credits, the vacancy stays a draft. Previously published adverts do not generate retroactive debits.

Credits are fictional. The app does not collect payment details, issue invoices or connect to a payment provider.
