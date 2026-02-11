import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom';
import 'bootstrap/dist/css/bootstrap.min.css';
import { AuthProvider, useAuth } from './context/AuthContext';
import { CartProvider } from './context/CartContext';
import Navigation from './components/Navigation';
import LoginView from './views/LoginView';
import ProductListView from './views/ProductListView';
import CartView from './views/CartView';
import OrderDetailsView from './views/OrderDetailsView';
import OrderView from './views/OrderView';
import AdminDashboard from './views/AdminDashboard';

const ProtectedRoute = ({ children, adminOnly = false }: { children: React.JSX.Element, adminOnly?: boolean }) => {
  const { isAuthenticated, role } = useAuth();
  if (!isAuthenticated) return <Navigate to="/login" replace />;
  if (adminOnly && role !== 'admin') return <Navigate to="/products" replace />;
  return children;
};

function AppRoutes() {
  return (
    <Router>
      <Navigation />
      <Routes>
        <Route path="/login" element={<LoginView />} />
        <Route path="/products" element={<ProductListView />} />
        <Route
          path="/cart"
          element={
            <ProtectedRoute>
              <CartView />
            </ProtectedRoute>
          }
        />
        <Route path="/orders" element={<ProtectedRoute><OrderView /></ProtectedRoute>} />
        <Route path="/orders/:id" element={<ProtectedRoute><OrderDetailsView /></ProtectedRoute>} />
        <Route path="/admin" element={<ProtectedRoute adminOnly><AdminDashboard /></ProtectedRoute>} />
        <Route path="/" element={<Navigate to="/products" replace />} />
      </Routes>
    </Router>
  );
}

function App() {
  return (
    <AuthProvider>
      <CartProvider>
        <AppRoutes />
      </CartProvider>
    </AuthProvider>
  );
}

export default App;
