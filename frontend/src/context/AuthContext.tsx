import React, { createContext, useContext, useState } from 'react';

interface AuthContextType {
    customerId: string | null;
    role: string | null;
    login: (id: string, role: string) => void;
    logout: () => void;
    isAuthenticated: boolean;
}

const AuthContext = createContext<AuthContextType | undefined>(undefined);

export const AuthProvider: React.FC<{ children: React.ReactNode }> = ({ children }) => {
    const [customerId, setCustomerId] = useState<string | null>(localStorage.getItem('customerId'));
    const [role, setRole] = useState<string | null>(localStorage.getItem('role'));

    const login = (id: string, r: string) => {
        setCustomerId(id);
        setRole(r);
        localStorage.setItem('customerId', id);
        localStorage.setItem('role', r);
    };

    const logout = () => {
        setCustomerId(null);
        setRole(null);
        localStorage.removeItem('customerId');
        localStorage.removeItem('role');
    };

    const isAuthenticated = !!customerId && !!role;

    return (
        <AuthContext.Provider value={{ customerId, role, login, logout, isAuthenticated }}>
            {children}
        </AuthContext.Provider>
    );
};

export const useAuth = () => {
    const context = useContext(AuthContext);
    if (context === undefined) {
        throw new Error('useAuth must be used within an AuthProvider');
    }
    return context;
};
