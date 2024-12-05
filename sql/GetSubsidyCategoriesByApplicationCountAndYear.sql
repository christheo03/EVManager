USE [vmarou01]
GO

/****** Object:  StoredProcedure [vmarou01].[GetSubsidyCategoriesByApplicationCountAndYear]    Script Date: 12/4/2024 4:16:27 AM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO


CREATE PROCEDURE [vmarou01].[GetSubsidyCategoriesByApplicationCountAndYear]
    @MinApplications INT,  -- Minimum number of applications required
    @Year INT              -- Year to check for applications
AS
BEGIN
    SET NOCOUNT ON;

    -- Assuming an index on date_of_app (especially covering index including subsidy_name and possibly application_count if available)
    DECLARE @StartDate DATE = DATEFROMPARTS(@Year, 1, 1);
    DECLARE @EndDate DATE = DATEFROMPARTS(@Year, 12, 31);

    -- Select subsidy categories with at least @MinApplications in the specified @Year
    SELECT 
        subsidy_name,
        COUNT(*) AS TotalApplications
    FROM 
        vmarou01.APPLICATION
    WHERE 
        date_of_app BETWEEN @StartDate AND @EndDate
    GROUP BY 
        subsidy_name
    HAVING 
        COUNT(*) >= @MinApplications
    ORDER BY 
        TotalApplications DESC;
END;
GO

