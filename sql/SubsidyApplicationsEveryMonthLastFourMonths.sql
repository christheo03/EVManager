USE [vmarou01]
GO

/****** Object:  StoredProcedure [vmarou01].[SubsidyApplicationsEveryMonthLastFourMonths]    Script Date: 12/4/2024 4:18:22 AM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO


CREATE PROCEDURE [vmarou01].[SubsidyApplicationsEveryMonthLastFourMonths]
AS
BEGIN
    SET NOCOUNT ON;

    DECLARE @StartDate DATE = DATEADD(MONTH, -4, EOMONTH(GETDATE(), -1)); -- Set to the start of the month four months ago
    DECLARE @EndDate DATE = EOMONTH(GETDATE()); -- Set to the end of the current month

    -- Select subsidy categories that had at least one application in each month of the last four months
    WITH MonthlyApplications AS (
        SELECT 
            subsidy_name,
            YEAR(date_of_app) AS Year,
            MONTH(date_of_app) AS Month,
            COUNT(*) AS ApplicationCount
        FROM 
            vmarou01.APPLICATION
        WHERE 
            date_of_app BETWEEN @StartDate AND @EndDate
        GROUP BY 
            subsidy_name, YEAR(date_of_app), MONTH(date_of_app)
    ),
    -- Count how many distinct months each subsidy category had applications
    DistinctMonthCounts AS (
        SELECT 
            subsidy_name,
            COUNT(*) AS DistinctMonthCount
        FROM 
            MonthlyApplications
        GROUP BY 
            subsidy_name
        HAVING 
            COUNT(*) = 4 -- Directly filter to only include those with entries in all four months
    )
    SELECT 
        subsidy_name
    FROM 
        DistinctMonthCounts
    ORDER BY 
        subsidy_name;
END;
GO

