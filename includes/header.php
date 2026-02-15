<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' | HallEase' : 'HallEase'; ?></title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Common CSS -->
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --text-color: #333;
            --bg-color: #f4f7f6;
            --sidebar-width: 250px;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        
        body { background-color: var(--bg-color); color: var(--text-color); }
        
        a { text-decoration: none; color: inherit; }
        ul { list-style: none; }
        
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            transition: transform 0.2s;
        }
        .btn:hover { transform: translateY(-2px); }
        
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 5px; }
        .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-danger { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        
        /* Dashboard Layout */
        .dashboard-container { display: flex; min-height: 100vh; }
        .sidebar { width: var(--sidebar-width); background: #2c3e50; color: white; padding: 20px; position: fixed; height: 100%; top: 0; left: 0; }
        .sidebar h2 { margin-bottom: 30px; text-align: center; color: #ecf0f1; }
        .sidebar ul li { margin-bottom: 15px; }
        .sidebar ul li a { display: flex; align-items: center; gap: 10px; padding: 10px; border-radius: 5px; transition: background 0.3s; color: #bdc3c7; }
        .sidebar ul li a:hover, .sidebar ul li a.active { background: rgba(255,255,255,0.1); color: white; }
        
        .main-content { margin-left: var(--sidebar-width); flex: 1; padding: 30px; }
        .top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; background: white; padding: 15px 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        
        .card { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        
        /* Forms */
        .form-group { margin-bottom: 20px; }
        .form-label { display: block; margin-bottom: 5px; font-weight: 500; }
        .form-control { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; }
        .form-control:focus { border-color: var(--primary-color); outline: none; }
        
        /* Tables */
        .table-container { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        th { font-weight: 600; color: #555; background: #f8f9fa; }
        
        .badge { padding: 5px 10px; border-radius: 20px; font-size: 12px; font-weight: 500; }
        .badge-success { background: #e6fffa; color: #047857; }
        .badge-warning { background: #fffbeb; color: #b45309; }
        .badge-danger { background: #fef2f2; color: #b91c1c; }
    </style>
</head>
<body>
