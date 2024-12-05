USE [vmarou01]
GO

/****** Object:  StoredProcedure [dbo].[GetUserDetails]    Script Date: 12/4/2024 4:12:50 AM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE PROCEDURE [dbo].[GetUserDetails]
    @username NVARCHAR(50)
AS
BEGIN
    SET NOCOUNT ON;

    SELECT user_id, email, password,role
    FROM [vmarou01].[SIMPLE_USER]
    WHERE username = @username;
END;
GO

