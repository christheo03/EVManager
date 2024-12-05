USE [vmarou01]
GO

/****** Object:  Trigger [vmarou01].[trg_UpdateTotalAmountOnStatusChange]    Script Date: 12/4/2024 4:32:22 AM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE TRIGGER [vmarou01].[trg_UpdateTotalAmountOnStatusChange]
ON [vmarou01].[STATUS]
AFTER UPDATE
AS
BEGIN
    SET NOCOUNT ON;

    -- Update the total_amount in the SUBSIDY table when the stage is changed to Expired or Rejected
    UPDATE s
    SET s.total_amount = s.total_amount + sub.amount
    FROM vmarou01.SUBSIDY s
    INNER JOIN vmarou01.APPLICATION app
        ON s.name = app.subsidy_name
    INNER JOIN Inserted i
        ON app.app_id = i.app_id
    INNER JOIN vmarou01.SUBSIDY sub
        ON s.name = sub.name
    WHERE i.stage IN ('Expired', 'Rejected');
END;
GO

ALTER TABLE [vmarou01].[STATUS] ENABLE TRIGGER [trg_UpdateTotalAmountOnStatusChange]
GO

