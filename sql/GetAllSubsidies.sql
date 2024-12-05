USE [vmarou01]
GO

/****** Object:  StoredProcedure [vmarou01].[GetAllSubsidies]    Script Date: 12/4/2024 4:14:42 AM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE PROCEDURE [vmarou01].[GetAllSubsidies]
AS
BEGIN
    SELECT name, description, amount, total_amount
    FROM SUBSIDY;
END;
GO

