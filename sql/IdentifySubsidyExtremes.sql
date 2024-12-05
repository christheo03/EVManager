USE [vmarou01]
GO

/****** Object:  StoredProcedure [vmarou01].[IdentifySubsidyExtremes]    Script Date: 12/4/2024 4:16:51 AM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO


CREATE PROCEDURE [vmarou01].[IdentifySubsidyExtremes]
AS
BEGIN
    SET NOCOUNT ON;

    -- Create a CTE to calculate the total disbursed amount per subsidy category for approved applications
    WITH SubsidyTotals AS (
        SELECT
            A.subsidy_name,
            SUM(S.amount) AS TotalDisbursed
        FROM vmarou01.APPLICATION A
        INNER JOIN vmarou01.SUBSIDY S ON A.subsidy_name = S.name
        INNER JOIN vmarou01.STATUS ST ON A.app_id = ST.app_id
        WHERE ST.stage = 'Approved'
        GROUP BY A.subsidy_name
    )
    -- Select the category with the highest subsidy amount
    , MaxSubsidy AS (
        SELECT TOP 1
            subsidy_name,
            TotalDisbursed
        FROM SubsidyTotals
        ORDER BY TotalDisbursed DESC
    )
    -- Select the category with the lowest subsidy amount
    , MinSubsidy AS (
        SELECT TOP 1
            subsidy_name,
            TotalDisbursed
        FROM SubsidyTotals
        ORDER BY TotalDisbursed ASC
    )
    -- Combine results to output both the highest and lowest subsidy categories
    SELECT 
        'Highest' AS Type,
        subsidy_name,
        TotalDisbursed
    FROM MaxSubsidy
    UNION ALL
    SELECT 
        'Lowest' AS Type,
        subsidy_name,
        TotalDisbursed
    FROM MinSubsidy;
END;
GO

