USE [vmarou01]
GO

/****** Object:  StoredProcedure [vmarou01].[GetApplicationPercentageByCategoryApproved]    Script Date: 12/4/2024 4:15:32 AM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO


CREATE PROCEDURE [vmarou01].[GetApplicationPercentageByCategoryApproved]
    @SubsidyNames NVARCHAR(MAX) = NULL, -- Optional: Comma-separated list of subsidy names
    @StartDate DATE = NULL, -- Optional: Start date for filtering applications
    @EndDate DATE = NULL  -- Optional: End date for filtering applications
AS
BEGIN
    SET NOCOUNT ON;

    -- Common Table Expression (CTE) to find the latest status for each application
    WITH LatestStatus AS (
        SELECT 
            app_id,
            MAX(date_of_modify) AS LastStatusDate
        FROM vmarou01.STATUS
        WHERE (@StartDate IS NULL OR date_of_modify >= @StartDate)
          AND (@EndDate IS NULL OR date_of_modify <= @EndDate)
        GROUP BY app_id
    )
    SELECT 
        A.subsidy_name,
        CAST(COUNT(*) AS FLOAT) / (SELECT COUNT(*) FROM vmarou01.APPLICATION A2
                                   INNER JOIN vmarou01.STATUS S2 ON A2.app_id = S2.app_id
                                   INNER JOIN LatestStatus LS2 ON S2.app_id = LS2.app_id AND S2.date_of_modify = LS2.LastStatusDate
                                   WHERE S2.stage = 'Approved'
                                     AND (@SubsidyNames IS NULL OR A2.subsidy_name IN (SELECT value FROM STRING_SPLIT(@SubsidyNames, ',')))
                                  ) * 100 AS PercentageOfTotal
    FROM 
        vmarou01.APPLICATION A
    INNER JOIN 
        vmarou01.STATUS ST ON A.app_id = ST.app_id
    INNER JOIN 
        LatestStatus LS ON ST.app_id = LS.app_id AND ST.date_of_modify = LS.LastStatusDate
    WHERE 
        ST.stage = 'Approved'
        AND (@SubsidyNames IS NULL OR A.subsidy_name IN (SELECT value FROM STRING_SPLIT(@SubsidyNames, ',')))
    GROUP BY 
        A.subsidy_name
    ORDER BY 
        PercentageOfTotal DESC;
END;
GO

