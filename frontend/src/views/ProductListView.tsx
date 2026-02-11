import React, { useState, useEffect } from 'react';
import { Container, Row, Col, Card, Form, InputGroup, Button, Badge, Spinner } from 'react-bootstrap';
import { Search, ShoppingCart, Check } from 'lucide-react';
import api from '../api/api';
import { useCart } from '../context/CartContext';
import { useAuth } from '../context/AuthContext';

interface Product {
    id: number;
    name: string;
    description: string;
    price: string;
    stock: number;
}

const ProductListView = () => {
    const [products, setProducts] = useState<Product[]>([]);
    const [search, setSearch] = useState('');
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState('');
    const [addedItems, setAddedItems] = useState<number[]>([]);

    const { addToCart } = useCart();
    const { role } = useAuth();

    const fetchProducts = async (searchTerm = '') => {
        setLoading(true);
        try {
            const response = await api.get('/api/products', {
                params: { search: searchTerm }
            });
            setProducts(response.data.products || response.data.data || []);
        } catch (err) {
            setError('Error al cargar productos');
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchProducts();
    }, []);

    const handleSearch = (e: React.FormEvent) => {
        e.preventDefault();
        fetchProducts(search);
    };

    const handleAddToCart = (product: Product) => {
        addToCart(product, 1);
        setAddedItems(prev => [...prev, product.id]);
        setTimeout(() => {
            setAddedItems(prev => prev.filter(id => id !== product.id));
        }, 2000);
    };

    return (
        <Container fluid className="mt-4 px-4">
            <Row className="mb-4 align-items-center">
                <Col md={6}>
                    <h2>Catálogo de Productos</h2>
                </Col>
                <Col md={6}>
                    <Form onSubmit={handleSearch}>
                        <InputGroup>
                            <Form.Control
                                placeholder="Buscar productos..."
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                            />
                            <Button variant="outline-primary" type="submit">
                                <Search size={18} />
                            </Button>
                        </InputGroup>
                    </Form>
                </Col>
            </Row>

            {error && <div className="alert alert-danger">{error}</div>}

            {loading ? (
                <div className="text-center mt-5">
                    <Spinner animation="border" variant="primary" />
                    <p className="mt-2">Cargando productos...</p>
                </div>
            ) : (
                <Row xs={1} md={2} lg={3} className="g-4">
                    {products.map((product) => (
                        <Col key={product.id}>
                            <Card className="h-100 shadow-sm border-0">
                                <Card.Body>
                                    <Card.Title className="fw-bold">{product.name}</Card.Title>
                                    <Card.Text className="text-muted small mb-2" style={{ height: '3rem', overflow: 'hidden' }}>
                                        {product.description}
                                    </Card.Text>
                                    <div className="d-flex justify-content-between align-items-center mt-3">
                                        <h5 className="text-primary mb-0">${parseFloat(product.price).toFixed(2)}</h5>
                                        <Badge bg={product.stock > 0 ? "success" : "danger"}>
                                            {product.stock > 0 ? `Stock: ${product.stock}` : "Sin stock"}
                                        </Badge>
                                    </div>
                                </Card.Body>
                                {role !== 'admin' && (
                                    <Card.Footer className="bg-white border-0 p-3">
                                        <Button
                                            variant={addedItems.includes(product.id) ? "success" : "primary"}
                                            className="w-100 d-flex align-items-center justify-content-center"
                                            disabled={product.stock === 0}
                                            onClick={() => handleAddToCart(product)}
                                        >
                                            {addedItems.includes(product.id) ? (
                                                <><Check size={18} className="me-2" /> Añadido</>
                                            ) : (
                                                <><ShoppingCart size={18} className="me-2" /> Añadir al Carrito</>
                                            )}
                                        </Button>
                                    </Card.Footer>
                                )}
                            </Card>
                        </Col>
                    ))}
                    {products.length === 0 && !loading && (
                        <Col xs={12} className="text-center mt-5">
                            <p className="h5 text-muted">No se encontraron productos.</p>
                        </Col>
                    )}
                </Row>
            )}
        </Container>
    );
};

export default ProductListView;
