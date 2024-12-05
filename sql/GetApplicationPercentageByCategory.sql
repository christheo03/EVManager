USE [vmarou01]
GO

/****** Object:  StoredProcedure [vmarou01].[GetApplicationPercentageByCategory]    Script Date: 12/4/2024 4:15:19 AM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO


CREATE PROCEDURE [vmarou01].[GetApplicationPercentageByCategory]
AS
BEGIN
    SET NOCOUNT ON;

    -- Calculate total number of applications
    DECLARE @TotalApplications FLOAT;
    SELECT @TotalApplications = COUNT(*) FROM vmarou01.APPLICATION;

    -- Return percentage of applications for each category, not showing the count
    SELECT 
        subsidy_name,
        (COUNT(*) / @TotalApplications) * 100 AS PercentageOfTotal
    FROM 
        vmarou01.APPLICATION
    GROUP BY 
        subsidy_name
    ORDER BY 
        PercentageOfTotal DESC;
END;
GO

