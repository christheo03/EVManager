USE [vmarou01]
GO

/****** Object:  StoredProcedure [vmarou01].[ShowAvailableGrants]    Script Date: 12/4/2024 4:18:06 AM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE PROCEDURE [vmarou01].[ShowAvailableGrants]
AS
BEGIN
    SELECT 
        CASE 
            WHEN amount != 0 THEN CAST(total_amount / amount AS INT)
            ELSE NULL
        END AS grants_available
    FROM 
        SUBSIDY; -- Replace with your actual table name
END;
GO

