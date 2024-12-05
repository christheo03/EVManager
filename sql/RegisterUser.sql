USE [vmarou01]
GO

/****** Object:  StoredProcedure [vmarou01].[RegisterUser]    Script Date: 12/4/2024 4:17:55 AM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE PROCEDURE [vmarou01].[RegisterUser]
	@user_id INT,
    @role NVARCHAR(40),
    @birthdate DATE,
    @first_name NVARCHAR(100),
    @last_name NVARCHAR(100),
    @username NVARCHAR(50),
    @password NVARCHAR(255),
    @email NVARCHAR(255),
    @address NVARCHAR(255),
    @nomiko_fisiko BIT
AS
BEGIN
    -- Check if the email already exists
    IF EXISTS (SELECT 1 FROM SIMPLE_USER WHERE email = @email)
    BEGIN
        RAISERROR ('The email is already registered. Please use a different email.', 16, 1);
        RETURN;
    END

    -- Check if the username already exists
    IF EXISTS (SELECT 1 FROM SIMPLE_USER WHERE username = @username)
    BEGIN
        RAISERROR ('The username is already taken. Please choose a different username.', 16, 1);
        RETURN;
    END

    -- Insert the new user
    INSERT INTO SIMPLE_USER (
        user_id,role, birthdate, first_name, last_name, username, password, email, address, nomiko_fisiko
    )
    VALUES (
        @user_id,@role, @birthdate, @first_name, @last_name, @username, @password, @email, @address, @nomiko_fisiko
    );

    -- Return success message (optional)
    PRINT 'User registered successfully!';
END;
GO

