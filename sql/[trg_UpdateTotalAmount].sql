USE [vmarou01]
GO

/****** Object:  Trigger [vmarou01].[trg_UpdateTotalAmount]    Script Date: 12/4/2024 4:31:37 AM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE TRIGGER [vmarou01].[trg_UpdateTotalAmount]
ON [vmarou01].[APPLICATION]
AFTER INSERT
AS
BEGIN
    SET NOCOUNT ON;

    -- Update the total_amount in the SUBSIDY table
    UPDATE s
    SET s.total_amount = s.total_amount - s.amount
    FROM vmarou01.SUBSIDY s
    INNER JOIN Inserted i
        ON s.name = i.subsidy_name
    WHERE i.subsidy_name IS NOT NULL;
END;
GO

ALTER TABLE [vmarou01].[APPLICATION] ENABLE TRIGGER [trg_UpdateTotalAmount]
GO

